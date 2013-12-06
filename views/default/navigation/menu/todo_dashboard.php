<?php
/**
 * Todo Dashboard Menu
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2013
 * @link http://www.thinkglobalschool.com/
 *
 * @uses $vars['infinite_scroll'] Enable infinite scrolling
 * @uses $vars['list_url']        List endpoint URL
 * @uses $vars['default_params']  Initial/default params
 */

// Pass vars on to to filtrate
echo elgg_view('navigation/menu/filtrate', $vars);