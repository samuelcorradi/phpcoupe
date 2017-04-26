<?php
/**
 * Created by PhpStorm.
 * User: samuelcorradi
 * Date: 06/01/15
 * Time: 15:54
 */

namespace Coupe\View;


class Composite
{

    protected $views = array();

    public function attachView(\Coupe\View $view)
    {

        if ( ! in_array($view, $this->views, TRUE) )
        {
            $this->views[] = $view;
        }

        return $this;

    }

    public function detachView(\Coupe\View $view)
    {

        $this->views = array_filter($this->views, function ($value) use ($view)
        {
            return $value !== $view;
        });

        return $this;

    }

    public function render()
    {

        $output = "";

        foreach ($this->views as $view)
        {
            $output .= $view->render();
        }

        return $output;

    }

}