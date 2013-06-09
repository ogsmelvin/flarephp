<?php

namespace FPHP\UI;

use FPHP\Fphp as A;

if(!function_exists('http_build_url')){
    A::mvc()->helper('url');
}

/**
 * 
 * @author anthony
 * 
 */
class Pagination
{
    /**
     * 
     * @var int
     */
    private $_page = null;

    /**
     * 
     * @var int
     */
    private $_offset = null;

    /**
     * 
     * @var int
     */
    private $_limit = null;

    /**
     * 
     * @var int
     */
    private $_total = null;

    /**
     * 
     * @var string
     */
    private $_url = null;

    /**
     * 
     * @var string
     */
    private $_alignment = null;

    /**
     * 
     * @var string
     */
    private $_size = null;

    /**
     * 
     * @param int $page
     * @return \FPHP\UI\Pagination
     */
    public function setCurrentPage($page)
    {
        $this->_page = (int) $page;
        return $this;
    }

    /**
     * 
     * @param int $limit
     * @return \FPHP\UI\Pagination
     */
    public function setLimitPerPage($limit)
    {
        $this->_limit = (int) $limit;
        return $this;
    }

    /**
     * 
     * @return int
     */
    public function getLimitPerPage()
    {
        return $this->_limit;
    }

    /**
     * 
     * @param int $count
     * @return \FPHP\UI\Pagination
     */
    public function setTotalCount($count)
    {
        $this->_total = (int) $count;
        return $this;
    }

    /**
     * 
     * @return int
     */
    public function getTotalCount()
    {
        return $this->_total;
    }

    /**
     * 
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->_page;
    }

    /**
     * 
     * @param int $offset
     * @return \FPHP\UI\Pagination
     */
    public function setOffset($offset)
    {
        $this->_offset = (int) $offset;
        return $this;
    }

    /**
     * 
     * @return int
     */
    public function getOffset()
    {
        return $this->_offset;
    }

    /**
     * 
     * @param string $url
     * @return \FPHP\UI\Pagination
     */
    public function setBaseUrl($url)
    {
        $this->_url = $url;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_url;
    }

    /**
     * 
     * @param string $alignment
     * @return \FPHP\UI\Pagination
     */
    public function setAlignment($alignment)
    {
        $this->_alignment = strtolower((string) $alignment);
        return $this;
    }

    /**
     * 
     * @param string $size
     * @return \FPHP\UI\Pagination
     */
    public function setSize($size)
    {
        $this->_size = strtolower((string) $size);
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getSize()
    {
        return $this->_size;
    }

    /**
     * 
     * @return string
     */
    public function getAlignment()
    {
        return $this->_alignment;
    }

    /**
     * 
     * @return string
     */
    public function createLinks()
    {
        if(!$this->_total || !$this->_limit){
            return $this->_wrap();
        }

        $num_pages = ceil($this->_total / $this->_limit);
        if(!$num_pages || (int) $num_pages === 1){
            return $this->_wrap();
        }
        $start = (($this->_page - 4) > 0) ? $this->_page - 4 : 1;
        $end = (($this->_page + 4) < $num_pages) ? $this->_page + 4 : $num_pages;
        $url = parse_url($this->_url);

        if(isset($url['query'])){
            parse_str($url['query'], $vars);
            unset($vars['page']);
            $vars['page'] = '';
            $url['query'] = http_build_query($vars);
        } else {
            $url['query'] = 'page=';
        }
        $url = http_build_url($url);

        $list = '<ul>';
        if($this->_page > 1){
            $list .= "<li><a data-page=\"1\" href=\"{$url}1\">First</a></li>";
            $list .= "<li><a data-page=\"".($this->_page - 1)."\" href=\"{$url}".($this->_page - 1)."\">Prev</a></li>";
        }
        foreach(range($start, $end) as $page){
            if($this->_page == $page){
                $list .= "<li class=\"active\"><a data-page=\"{$page}\" href=\"{$url}{$page}\">".$page."</a></li>";
            } else {
                $list .= "<li><a data-page=\"{$page}\" href=\"{$url}{$page}\">".$page."</a></li>";
            }
        }
        if($this->_page < $num_pages){
            $list .= "<li><a data-page=\"".($this->_page + 1)."\" href=\"{$url}".($this->_page + 1)."\">Next</a></li>";
            $list .= "<li><a data-page=\"{$num_pages}\" href=\"{$url}{$num_pages}\">Last</a></li>";
        }
        $list .= '</ul>';
        return $this->_wrap($list);
    }

    /**
     * 
     * @param string $list
     * @return string
     */
    private function _wrap($list = '')
    {
        $wrapper = "<div class=\"pagination";
        $wrapper .= $this->_size ? ' pagination-'.$this->_size : '';
        $wrapper .= $this->_alignment ? ' pagination-'.$this->_alignment : '';
        $wrapper .= "\">{$list}</div>";
        return $wrapper;
    }

    /**
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->createLinks();
    }
}
