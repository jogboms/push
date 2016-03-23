<?php
/**
 * PUSH MVC Framework.
 * @package See composer.json
 * @version See composer.json
 * @author See composer.json
 * @copyright See composer.json
 * @license See composer.json
 */

namespace Push\Views;

use Push\Exceptions\ViewException;

Class Php extends \Push\View 
{
    protected 
        $extension = '.tpl.php';

    public function initialize()
    {
        $this->setRoot(TEMPLATE_PATH);
    }
    
    public function getEnvironment()
    {
        return $this;
    }
    
    protected function make($filename, $as_string = true)
    {
        extract($this->params());
        ob_start();

        include $filename;

        $return = ob_get_contents();
        if(ob_get_length()) ob_end_clean();

        return $return;
    }

    /*
    Chainable method call : $this->import('file')->import('file2')->import('file3')
     */
    public function import($filename, array $variables = [])
    {
        echo $this->render($filename, $variables);
        return $this;
    }
    public function section($name, $content = null, $variables = [])
    {
        if((is_null($content) || is_array($content))
         && array_key_exists($name, $this->_sections)){
            return $this->render($this->_sections[$name]['content'], ($this->_sections[$name]['vars']+$variables));
        }
        elseif(!is_null($content)){
            $this->$name = true;
            $this->_sections[$name] = ['content'=>$content, 'vars'=>$variables];
            return $this;
        }
        return false;
    }

    /**
     * Render a Template file
     * @param  string  $filename      
     * @param  array  $variables      Additional variable to pass to template
     *                                If set to true or false, It acts as the option for $use_layout
     * @param  boolean $use_layout Use Layout.tpl.php as Base Template
     * @return string Contents of template file
     */
    public function parse($filename, $variables = [], $use_layout = false)
    {
        if(is_array($variables)){
            $this->assign($variables);
        }

        // Passing in an actual filepath that exists [without the extension]
        if(is_file($filename)){
            return $this->make($filename);
        }

        $filename = $this->templateDir.DIRECTORY_SEPARATOR.$filename;

        if(!file_exists($filename)){
            throw new ViewException('Missing Template file: `'.$filename.'` in `'.trim_root(dirname($filename)).'`');
        }

        if(is_bool($variables)){
            $use_layout = $variables;
        }

        if($use_layout === false)
            return $this->make($filename);

        $this->assign('__content__', $this->make($filename));
        return $this->make($this->templateDir.DIRECTORY_SEPARATOR.'layout'.$this->extension);
    }

    public function assign($key, $value = '')
    {
        return $this->set($key, $value);
    }

}
