<?php
namespace B13\Amazingshortlinks\Controller;

/***************************************************************
 *  Copyright notice - MIT License (MIT)
 *
 *  (c) 2013 b:dreizehn GmbH,
 * 		Benjamin Mack <benjamin.mack@b13.de>
 *  All rights reserved
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is
 *  furnished to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in
 *  all copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 ***************************************************************/

/**
 * Controller to do all the logic when managing / administrating short links
 *
 */
class ManagementController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * domain repository
	 * @var \B13\Amazingshortlinks\Domain\Repository\DomainRepository
	 * @inject
	 */
	protected $domainRepository;

	/**
	 * repository with all links, is usually limited to a specific domain
	 * @var \B13\Amazingshortlinks\Domain\Repository\LinkRepository
	 * @inject
	 */
	protected $linkRepository;

	/**
	 * list all existing short links
	 *
	 * @param \B13\Amazingshortlinks\Domain\Model\Domain $selectedDomain
	 * @param \B13\Amazingshortlinks\Domain\Model\Link $shortLink
	 * @dontvalidate $selectedDomain
	 * @dontvalidate $shortLink
	 */
	public function indexAction(\B13\Amazingshortlinks\Domain\Model\Domain $selectedDomain = NULL, \B13\Amazingshortlinks\Domain\Model\Link $shortLink = NULL) {

			// select a domain to work on first
		$allShortlinkDomains = $this->domainRepository->findAllValidShortLinkDomains();
		$this->view->assign('allShortlinkDomains', $allShortlinkDomains);

		// if the domain is selected, this data
		// is stored for the current session
		if ($selectedDomain != NULL) {
			$this->setSelectedDomain($selectedDomain);
		} else {
			// selected domain is null => none selected
			// then it is checked if there is something in the session already
			$selectedDomain = $this->getSelectedDomain();
		}

		if ($selectedDomain !== NULL) {
			// find all links in use on this  domain
			$existingLinks = $this->linkRepository->findAllByDomain($selectedDomain);
			$this->view->assign('existingLinks', $existingLinks);
			$this->view->assign('selectedDomain', $selectedDomain);

			if ($shortLink === NULL) {
				$shortLink = $this->objectManager->create('B13\\Amazingshortlinks\\Domain\\Model\\Link');
			}
			$shortLink->setDomainrecord($selectedDomain);
			$this->view->assign('shortLink', $shortLink);

			// @todo: find a better way to create a random string
			$defaultPath = \TYPO3\CMS\Core\Utility\GeneralUtility::shortMD5(time(), 10);
			$this->view->assign('defaultPath', $defaultPath);
		}
	}

	/**
	 * adds a new short link to the DB
	 *
	 * @param \B13\Amazingshortlinks\Domain\Model\Link $shortLink
	 * @param string $linktype can be "page" or "external"
	 * @param string $destinationexternal the URL target for the external redirect
	 * @param string $destinationpage the page uid to link to (if linktype is "page")
	 * @param boolean $includesubpages
	 */
	public function addAction(\B13\Amazingshortlinks\Domain\Model\Link $shortLink, $linktype, $destinationexternal = NULL, $destinationpage = NULL, $includesubpages = NULL) {
		if ($linktype === 'external') {

			$shortLink->setDestination($destinationexternal);
			$this->addShortlinkToRepository($shortLink);

		} else if (intval($destinationpage) > 0) {
			// resolve the page UID to a full URL
			$finalUrl = $this->buildFullUrlForPage(intval($destinationpage));
			if ($finalUrl) {
				$shortLink->setDestination($finalUrl);
				$this->addShortlinkToRepository($shortLink);

				if ($includesubpages) {
					// fetch all subpages
					$subpages = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid, tx_realurl_pathsegment, title', 'pages', 'deleted=0 AND hidden=0 AND pid=' . intval($destinationpage), '', 'sorting ASC');
					foreach ($subpages as $subpageData) {
						$subpageShortLink = $this->objectManager->create('B13\\Amazingshortlinks\\Domain\\Model\\Link');

						$suffix = strtolower($subpageData['tx_realurl_pathsegment'] ? $subpageData['tx_realurl_pathsegment'] : $subpageData['title']);
						$suffix = str_replace(' ', '-', $suffix);
						$subpageShortLink->setShortPath($shortLink->getShortPath() . '/' . $suffix);
						$finalUrl = $this->buildFullUrlForPage($subpageData['uid']);
						if ($finalUrl) {
							$subpageShortLink->setDestination($finalUrl);
							$this->addShortlinkToRepository($subpageShortLink);
						}
					}
				}
			}
		}

		$this->redirect('index');
	}

	/**
	 * removes a short link from the DB
	 *
	 * @param \B13\Amazingshortlinks\Domain\Model\Link $shortLink
	 */
	public function removeAction(\B13\Amazingshortlinks\Domain\Model\Link $shortLink) {

		$this->linkRepository->remove($shortLink);

		// add success message
		$this->flashMessageContainer->add(
			'The short URL "' . $shortLink->getFullShortUrl() . '" was removed.',
			'Short URL removed',
			\TYPO3\CMS\Core\Messaging\FlashMessage::OK
		);
		$this->redirect('index');
	}



	/**
	 * gets the currently selected domain (if stored in session)
	 * similar to existing "modfunc" / "modmenu" options
	 *
	 * @return \B13\Amazingshortlinks\Domain\Model\Domain the domain object
	 */
	protected function getSelectedDomain() {
		$selectedDomain = NULL;
		// Getting stored user-data from this module:
		$settings = $GLOBALS['BE_USER']->getModuleData('tx_amazingshortlinks_management', 'ses');
		if ($settings['domain'] > 0) {
			$selectedDomain = $this->domainRepository->findByUid($settings['domain']);	
		}
		$this->view->assign('selectedDomain', $selectedDomain);
		return $selectedDomain;
	}

	/**
	 * sets a domain record to be stored in session so it can be used for later calls
	 * simiar to existing "modfunc" / "modmenu" options
	 * 
	 * @param \B13\Amazingshortlinks\Domain\Model\Domain $selectedDomain
	 */
	protected function setSelectedDomain(\B13\Amazingshortlinks\Domain\Model\Domain $selectedDomain) {

		$settings = array(
			'domain' => $selectedDomain->getUid()
		);

		$GLOBALS['BE_USER']->pushModuleData('tx_amazingshortlinks_management', $settings);
	}


	/** 
	 * wrapper function to add a new short link to the repository
	 * with some basic checks, setting the domain record from the current sesssion,
	 * and adding a flash message directly
	 */
	protected function addShortlinkToRepository($shortLink) {

		$selectedDomain = $this->getSelectedDomain();
		$shortLink->setDomainrecord($selectedDomain);

		// destintation is empty
		if ($shortLink->getDestination() == '') {
			$this->flashMessageContainer->add(
				'The short URL "' . $shortLink->getFullShortUrl() . '" was not created, because the destination is not set.',
				'Destination empty',
				\TYPO3\CMS\Core\Messaging\FlashMessage::ERROR
			);

		// short path already exists
		} elseif ($this->linkRepository->shortPathExistsInDomain($shortLink->getShortpath(), $selectedDomain)) {
			$this->flashMessageContainer->add(
				'The short URL "' . $shortLink->getFullShortUrl() . '" was not created, because a short URL with the same short path already exists.',
				'Short URL already exists',
				\TYPO3\CMS\Core\Messaging\FlashMessage::ERROR
			);

		} else {
			// set the final properties
			$shortLink->setPid($selectedDomain->getPid());
			$shortLink->setCreatedon(new \DateTime());

			$this->linkRepository->add($shortLink);

			// success
			$this->flashMessageContainer->add(
				'The short URL "' . $shortLink->getFullShortUrl() . '" was added and can be used now.',
				'Short URL added',
				\TYPO3\CMS\Core\Messaging\FlashMessage::OK
			);
		}
	}


	/**
	 * creates a URL for the frontend
	 * and builds a complete TSFE object
	 * 
	 * @param integer $pageUid the page to link to
	 * @return string the full absolute URL to the page
	 */
	protected function buildFullUrlForPage($pageUid) {
		if ($GLOBALS['TSFE'] == NULL) {
			$GLOBALS['TT'] = new \TYPO3\CMS\Core\TimeTracker\NullTimeTracker();
			$GLOBALS['TSFE'] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController', $GLOBALS['TYPO3_CONF_VARS'], $pageUid, 0);
			$GLOBALS['TSFE']->initFeUser();
			$GLOBALS['TSFE']->fetch_the_id();
			$GLOBALS['TSFE']->getCompressedTCarray();					
			$GLOBALS['TSFE']->initTemplate();
			$GLOBALS['TSFE']->getConfigArray();
			$GLOBALS['TSFE']->config['config']['typolinkEnableLinksAcrossDomains'] = 1;
		}

		return $this->uriBuilder->setCreateAbsoluteUri(TRUE)->setTargetPageUid($pageUid)->buildFrontendUri();
	}

}