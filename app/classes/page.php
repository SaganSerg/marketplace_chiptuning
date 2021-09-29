<?php
abstract class Page 
{
    protected $name;

    function __construct(string $name)
    {
        $this->name = $name;
    }

    protected function getRequerMyself() // Эта функция будет не нужна удалить наверное
    {
        return '/' . strtolower($this->pageName);
    }

    protected function getHiddenInput($value, $name)
    {
        return $this->getInput('hidden', $value, $name);
    }
    protected function getHiddenInputBlock(array $attr)
    {
        $html = '';
        if ($attr) {
            foreach ($attr as $elem) {
                $html .= "<input type='hidden' name='" . $elem['name'] . "' value='" . $elem['value'] . "'>\n";
            }
        }
        return $html;
    }
    protected function getInput(string $type = null, string $value = null, string $name = null, string $class = null, string $placeholder = null)
    {
        return "<input type='$type' value='$value' name='$name' class='$class' placeholder='$placeholder'>";
    }
    protected function saveInputValue($inputName)
    {
        if (isset($_POST[$inputName])) {
            return $_POST[$inputName];
        }
        return '';
    }
    function __set($property, $value) 
    {
        $this->$property = $value;
    }
    function __get($property)
    {
        if (isset($this->$property)) return $this->$property;
        return null;
    }
}