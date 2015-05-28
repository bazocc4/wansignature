<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
CakePlugin::routes();

/**
 * Rss Initialization
 */
Router::parseExtensions('rss'); 

$server = $_SERVER['SERVER_NAME'];
$server = strtolower($server);

$accessMode = 'web';

// the domain contain www, redirect to the corrent one
if (substr($server, 0, 4) == 'www.') {
	$server = substr($server, 4);
}

$controllers = array(
	'accounts',
	'configs',
	'entries',	
	'entry_metas',
	'pages',
	'roles',	
	'settings',
	'type_metas',
	'types',
	'user_metas',
	'users',
	'admin',
	'instant_payment_notifications',
);

$requestUri = $_SERVER['REQUEST_URI'];
$url_set = explode('/', strtolower($requestUri));

// -------------- THIS IS FOR LOCAL HOST ------------------------------------ //
if(isLocalhost())
{
	$controller = !empty($url_set[2]) ? $url_set[2] : null;
	$action     = !empty($url_set[3]) ? $url_set[3] : null;
}
else // -------------- THIS IS FOR ONLINE HOSTING -------------------------------- //
{
	$controller = !empty($url_set[1]) ? $url_set[1] : null;
	$action     = !empty($url_set[2]) ? $url_set[2] : null;
}

$domainSet = explode('.', $server);

// ---------------------------- PAYPAL API CALLBACK ---------------------------------------- //
Router::connect('/paypal_ipn/process', array('controller' => 'instant_payment_notifications', 'action' => 'process'));
// ---------------------------- LOGIN ROUTING ---------------------------------------------- //
Router::connect('/admin', array('controller' => 'accounts', 'action' => 'redirect_login'));
Router::connect(($controller == "admin"?'/admin':'').'/login', array('controller' => 'accounts', 'action' => 'login'));
Router::connect(($controller == "admin"?'/admin':'').'/forget', array('controller' => 'accounts', 'action' => 'forget'));
// ------------------------- END OF LOGIN ROUTING ------------------------------------------ //

// ---------------------------- BACKUP DATABASE ---------------------------------------------- //
Router::connect('/admin/entries/backup', array('controller' => 'entries', 'action' => 'backup' , 'admin'=>true));
Router::connect('/admin/entries/backup/:mode', array('controller' => 'entries', 'action' => 'backup' , 'admin'=>true));
// ---------------------------- END OF BACKUP DATABASE ---------------------------------------------- //

if(in_array($controller, $controllers))
{
	if($controller == "admin")
	{
		if($action == "master")
		{
			Router::connect('/admin/master/types/*', array('controller' => 'types', 'action' => 'master' , 'admin'=>true));
			Router::connect('/admin/master/roles/*', array('controller' => 'roles', 'action' => 'master' , 'admin'=>true));
		}
		else if($action == "entries")
		{
			// go to parent entries...
			Router::connect('/admin/entries/:type', array('controller' => 'entries', 'action' => 'index' , 'admin'=>true));
			Router::connect('/admin/entries/:type/index/:page', array('controller' => 'entries', 'action' => 'index' , 'admin'=>true));
			Router::connect('/admin/entries/:type/add', array('controller' => 'entries', 'action' => 'index_add' , 'admin'=>true));
			Router::connect('/admin/entries/:type/edit/:entry', array('controller' => 'entries', 'action' => 'index_edit' , 'admin'=>true));
			
			// go to children entries...
			Router::connect('/admin/entries/:type/:entry', array('controller' => 'entries', 'action' => 'index' , 'admin'=>true));
			Router::connect('/admin/entries/:type/:entry/index/:page', array('controller' => 'entries', 'action' => 'index' , 'admin'=>true));
			Router::connect('/admin/entries/:type/:entry/add', array('controller' => 'entries', 'action' => 'index_add' , 'admin'=>true));
			Router::connect('/admin/entries/:type/:entry_parent/edit/:entry', array('controller' => 'entries', 'action' => 'index_edit' , 'admin'=>true));
		}
	}
}
else
{
	// --------------------------  SHOPPING CART ---------------------------- //
	Router::connect('/shoppingcart/:step', array('controller' => 'instant_payment_notifications', 'action' => 'shoppingcart'));
	
	// ---------------------------- STAGGING ROUTING ---------------------------------------------- //
	Router::connect('/:type/add', array('controller' => 'entries', 'action' => 'index_add'));
	Router::connect((strlen($controller) == 2?'/:lang/:type':'/:type/:entry').'/add', array('controller' => 'entries', 'action' => 'index_add'));
	Router::connect('/:lang/:type/:entry/add', array('controller' => 'entries', 'action' => 'index_add'));
	
	Router::connect('/:type/edit/:entry', array('controller' => 'entries', 'action' => 'index_edit'));
	Router::connect((strlen($controller) == 2?'/:lang/:type':'/:type/:entry_parent').'/edit/:entry', array('controller' => 'entries', 'action' => 'index_edit'));
	Router::connect('/:lang/:type/:entry_parent/edit/:entry', array('controller' => 'entries', 'action' => 'index_edit'));
	// ------------------------ END OF STAGGING ROUTING ---------------------------------------------- //
	
	// LAST DECISION !!
	Router::connect('/*', array('controller' => 'entries', 'action' => 'index'));
	// Router::connect('/*', array('controller' => 'settings', 'action' => 'error404'));
}

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
require CAKE . 'Config' . DS . 'routes.php';