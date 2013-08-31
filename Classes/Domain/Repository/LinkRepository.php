<?php
namespace B13\Amazingshortlinks\Domain\Repository;

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
 * The repository for the domain model "Link"
 *
 * @package B13\Amazingshortlinks
 * @subpackage Domain\Repository
 */
class LinkRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

	/**
	 * finds all link records of a specific domain
	 *
	 * @param \B13\Amazingshortlinks\Domain\Model\Domain $domain
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findAllByDomain(\B13\Amazingshortlinks\Domain\Model\Domain $domain) {
		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectStoragePage(FALSE);
		$query->matching(
			$query->equals('domainrecord', $domain)
		);
		
		$orderings = array(
			'createdon' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING
		);
		$query->setOrderings($orderings);
		return $query->execute();
	}

	/**
	 * checks if a short link already exists in the domain. this is not allowed
	 *
	 * @param string $shortpath the string to search for
	 * @param \B13\Amazingshortlinks\Domain\Model\Domain $domain
	 * @return boolean
	 */
	public function shortPathExistsInDomain($shortpath, $domain) {
		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectStoragePage(FALSE);
		$query->matching(
			$query->logicalAnd(
				$query->equals('shortpath', $shortpath),
				$query->equals('domainrecord', $domain)
			)
		);

		return ($query->execute()->count() > 0);
	}
}
