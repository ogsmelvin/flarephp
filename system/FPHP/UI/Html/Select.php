<?php

namespace FPHP\UI\Html;

use FPHP\UI\Html\Element;

/**
 * 
 * @author anthony
 * 
 */
class Select extends Element
{
    /**
     * 
     * @var string
     */
    private $_textKey = null;

    /**
     * 
     * @var string
     */
    private $_valueKey = null;

    /**
     * 
     * @var string
     */
    private $_groupKey = null;

    /**
     * 
     * @var string
     */
    private $_selected = null;

    /**
     * 
     * @param string $key
     * @return \FPHP\UI\Html\Select
     */
    public function setValueKey($key)
    {
        $this->_valueKey = (string) $key;
        return $this;
    }

    /**
     * 
     * @param string $key
     * @return \FPHP\UI\Html\Select
     */
    public function setTextKey($key)
    {
        $this->_textKey = (string) $key;
        return $this;
    }

    /**
     * 
     * @param string $val
     * @return \FPHP\UI\Html\Select
     */
    public function setSelectedValue($val)
    {
        $this->_selected = (string) $val;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getTextKey()
    {
        return $this->_textKey;
    }

    /**
     * 
     * @return string
     */
    public function getValueKey()
    {
        return $this->_valueKey;
    }

    /**
     * 
     * @return mixed
     */
    public function getSelectedValue()
    {
        return $this->_selected;
    }

    /**
     * 
     * @param string $key
     * @return \FPHP\UI\Html\Select
     */
    public function setGroupKey($key)
    {
        $this->_groupKey = (string) $key;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function render()
    {
        if(!$this->_textKey || !$this->_valueKey){
            throw new Exception("_textKey or _valueKey is not defined");
        }
        $str = "<select name=\"{$this->_name}\"";
        if($this->_attrs){
            foreach($this->_attrs as $key => $attr){
                $str .= " {$key}=\"{$attr}\" ";
            }
        }
        $str .= ">";
        if(!$this->_groupKey){
            foreach($this->_data as $data){
                if($this->_selected === (string) $data->{$this->_valueKey}){
                    $str .= "<option selected=\"selected\" value=\"".html($data->{$this->_valueKey})."\">".html($data->{$this->_textKey})."</option>";
                } else {
                    $str .= "<option value=\"".html($data->{$this->_valueKey})."\">".html($data->{$this->_textKey})."</option>";
                }
            }
        } else {
            $groupStr = array();
            foreach($this->_data as $data){
                $groupStr[$data->{$this->_groupKey}] = isset($groupStr[$data->{$this->_groupKey}]) ? $groupStr[$data->{$this->_groupKey}] : array();
                if($this->_selected === (string) $data->{$this->_valueKey}){
                    $groupStr[$data->{$this->_groupKey}][] .= "<option selected=\"selected\" value=\"".html($data->{$this->_valueKey})."\">".html($data->{$this->_textKey})."</option>";
                } else {
                    $groupStr[$data->{$this->_groupKey}][] .= "<option value=\"".html($data->{$this->_valueKey})."\">".html($data->{$this->_textKey})."</option>";
                }
                
            }
            foreach($groupStr as $key => $group){
                $str .= "<optgroup label=\"".html($key)."\">".implode('', $group)."</optgroup>";
            }
            unset($groupStr);
        }
        $str .= "</select>";
        return $str;
    }
}