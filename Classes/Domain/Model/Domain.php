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
 * The domain model of a Web Domain Name
 *
 * @package B13\Amazingshortlinks
 * @subpackage Domain\Model
 * @entity
 */
class Domain extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * the name of the domain
	 * @var string
	 */
	protected $domainname;

	/**
	 * checked if the domain should be used as a short link domain
	 * @var \boolean
	 */
	protected $txAmazingshortlinksEnable;


	/**
	 * sets the domain name attribute
	 * 
	 * @param	string	 $domainname
	 * @return	void
	 */
	public function setDomainname($domainname) {
		$this->domainname = $domainname;
	}

	/**
	 * returns the domain name attribute
	 * 
	 * @return	string
	 */
	public function getDomainname() {
		return $this->domainname;
	}

	/**
	 * sets the txAmazingshortlinksEnable attribute
	 * 
	 * @param	\boolean	$txAmazingshortlinksEnable
	 * @return	void
	 */
	public function setTxAmazingshortlinksEnable($txAmazingshortlinksEnable) {
		$this->txAmazingshortlinksEnable = $txAmazingshortlinksEnable;
	}

	/**
	 * returns the txAmazingshortlinksEnable attribute
	 * 
	 * @return	\boolean
	 */
	public function getTxAmazingshortlinksEnable() {
		return $this->txAmazingshortlinksEnable;
	}

}
