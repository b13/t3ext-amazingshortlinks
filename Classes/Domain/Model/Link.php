<?php
namespace B13\Amazingshortlinks\Domain\Model;

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
 * The domain model of a Short Link
 *
 * @package B13\Amazingshortlinks
 * @subpackage Domain\Model
 * @entity
 */
class Link extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {
	
	/**
	 * the domain that it belongs to
	 * @var \B13\Amazingshortlinks\Domain\Model\Domain
	 */
	protected $domainrecord;

	/**
	 * the string, the relative URL of the domain that should be tracked on
	 * @var \string
	 */
	protected $shortpath;

	/**
	 * the redirect destination / target of the short link
	 * @var \string
	 */
	protected $destination;

	/**
	 * the creation date time object
	 * @var \DateTime
	 */
	protected $createdon;

	/**
	 * sets the domainrecord attribute
	 * 
	 * @param	\B13\Amazingshortlinks\Domain\Model\Domain	 $domainrecord
	 * @return	void
	 */
	public function setDomainrecord(\B13\Amazingshortlinks\Domain\Model\Domain $domainrecord) {
		$this->domainrecord = $domainrecord;
	}

	/**
	 * returns the domainrecord attribute
	 * 
	 * @return	\B13\Amazingshortlinks\Domain\Model\Domain
	 */
	public function getDomainrecord() {
		return $this->domainrecord;
	}

	/**
	 * sets the shortpath attribute
	 * 
	 * @param	\string	 $shortpath
	 * @return	void
	 */
	public function setShortpath($shortpath) {
		$this->shortpath = $shortpath;
	}

	/**
	 * returns the shortpath attribute
	 * 
	 * @return	\string
	 */
	public function getShortpath() {
		return $this->shortpath;
	}

	/**
	 * returns the complete URI to the short link
	 * ready to copy/paste
	 * 
	 * @return string
	 */
	public function getFullShortUrl() {
		$fullUrl = $this->getDomainrecord()->getDomainname();
		$urlParts = parse_url($fullUrl);
		if (!$urlParts['scheme']) {
			$fullUrl = 'http://'  . $fullUrl;
		}

		$fullUrl = rtrim($fullUrl, '/') . '/' . $this->getShortpath();
		return $fullUrl;
	}

	/**
	 * sets the destination attribute
	 * 
	 * @param	\string	 $destination
	 * @return	void
	 */
	public function setDestination($destination) {
		$urlParts = parse_url($destination);
		if (!$urlParts['scheme']) {
			$destination = 'http://'  . $destination;
		}
		$this->destination = $destination;
	}

	/**
	 * returns the destination attribute
	 * 
	 * @return	\string
	 */
	public function getDestination() {
		return $this->destination;
	}

	/**
	 * sets the created on attribute
	 * 
	 * @param	\DateTime	 $createdon
	 * @return	void
	 */
	public function setCreatedon($createdon) {
		$this->createdon = $createdon;
	}

	/**
	 * returns the created on attribute
	 * 
	 * @return	\DateTime
	 */
	public function getCreatedon() {
		return $this->createdon;
	}


	/**
	 * generic function to fetch all labels of a select in a localized version
	 *
	 * @param string $field the field/attribute
	 * @param string $addEmptyLabel whether to add an empty label as first entry
	 * @param string $table the database table of the TCA (maybe could be detected in another way)
	 * @param string $extensionName the name of the extension (used to look up the translation part for "pleaseSelect" (maybe could be detected in another way)
	 * @return the associative array with all labels
	 */
	public function getAllLabelsForField($field, $addEmptyLabel = FALSE, $table = 'Tx_Amazingshortlinks_Domain_Model_Link', $extensionName = 'Amazingshortlinks') {
		if ($addEmptyLabel) {
			$labels = array(Tx_Extbase_Utility_Localization::translate('forms.pleaseSelect', strtolower($extensionName)));
		} else {
			$labels = array();
		}

		if ($GLOBALS['TSFE']) {
			$GLOBALS['TSFE']->includeTCA();
		}

		$table = strtolower($table);

		t3lib_div::loadTCA($table);
		foreach ($GLOBALS['TCA'][$table]['columns'][$field]['config']['items'] as $item) {
			if ($item[1] == '') {
				continue;
			}
			if ($GLOBALS['TSFE']) {
				$labels[$item[1]] = $GLOBALS['TSFE']->sL($item[0]);
			} else {
				$labels[$item[1]] = $GLOBALS['LANG']->sL($item[0]);
			}
		}

		return $labels;
	}

}
