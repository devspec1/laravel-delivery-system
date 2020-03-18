<?php

/**
 * Image Handler Interface
 *
 * @package     Gofer
 * @subpackage  Contracts
 * @category    Image Handler
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
*/

namespace App\Contracts;

interface ImageHandlerInterface
{
	public function upload($image, $options);
	public function delete($image);
	public function getImage($file_name, $options);
}