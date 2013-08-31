<?php
namespace B13\Amazingshortlinks\Hook;

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
 * Hook class to look the shortcut up, if the domain record
 * if the current domain is used for target
 */
class FrontendControllerHook {

	/**
	 * main hook to check if the current domain name matches any sys_domain record that is used for short links
	 *
	 * @param array $parameters an array of hook parameters, not used right now
	 * @param \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $parentObject the instance of TSFE
	 */
	public function checkForShortlink(&$parameters, $parentObject) {
		$currentUrl = \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL');
		$urlParts = parse_url($currentUrl);
		$path = trim($urlParts['path'], '/');

		if ($path) {
		
			// find the short link if there is a shortlink domain registered
			$redirectRecord = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(
				'l.uid, l.destination, d.domainname',
				'tx_amazingshortlinks_domain_model_link l, sys_domain d',
				'l.domainrecord=d.uid AND d.hidden=0 AND l.deleted=0 AND l.hidden=0 AND l.starttime<' . time() . ' AND (l.endtime=0 OR l.endtime>' . time() . ')'
					. ' AND d.tx_amazingshortlinks_enable=1 '
					. ' AND d.domainname=' . $GLOBALS['TYPO3_DB']->fullQuoteStr(\TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_HOST_ONLY'), 'sys_domain')
					. ' AND l.shortpath=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($path, 'tx_amazingshortlinks_domain_model_link')
			);
	
			// if a record was found, do the redirect
			if ($redirectRecord !== FALSE && $redirectRecord !== NULL) {

				// track the hit
				$logRecord = array(
					'creationdate' => time(),
					'ipaddress'    => \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('REMOTE_ADDR'),
					'useragent'    => \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('HTTP_USER_AGENT'),
					'referer'      => \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('HTTP_REFERER'),
					// can be something like "pdf" or something like that
					'shortlink'    => $redirectRecord['uid'],
					'query'        => $urlParts['query']
				);
				$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_amazingshortlinks_domain_model_log', $logRecord);
				
				
				\TYPO3\CMS\Core\Utility\HttpUtility::redirect($redirectRecord['destination'], \TYPO3\CMS\Core\Utility\HttpUtility::HTTP_STATUS_301);
			}
			
		}
	}

}