<?php defined('IN_CMS') or die('No direct access allowed.');

/*
	This is the front controller that 
	will route all incoming requests
*/
class Anchor {

	private static function setup() {
		// Query metadata and store into our config
		$sql = "select `key`, `value` from meta";
		$meta = array();

		foreach(Db::results($sql) as $row) {
			$meta[$row->key] = $row->value;
		}

		Config::set('metadata', $meta);

		// Store which page will host our posts
		$page = Pages::find(array('id' => Config::get('metadata.show_posts')));
		IoC::instance('postspage', $page, true);
	}

	public static function run() {
		// run setup and prepare env
		static::setup();
		
		// handle the requested uri
		$uri = static::parse();
		$segments = array();
		
		if(strlen($uri)) {
			$segments = explode('/', $uri);
		}

		// default to posts when no action is set
		$action = count($segments) ? array_shift($segments) : 'posts';
		
		// default to our front end router
		$controller = 'Routes';
		
		// set the template path
		$theme = Config::get('metadata.theme');
		Template::path(PATH . 'themes/' . $theme . '/');
		
		// remove admin as an argument and set the default action if there isnt one
		if($action == 'admin') {
			// set default controller for the admin
			$controller = (count($segments) ? array_shift($segments) : 'posts') . '_controller';

			// set default action
			$action = count($segments) ? array_shift($segments) : 'index';

			// public admin actions
			$public = array('users/login', 'users/amnesia', 'users/reset');
			
			// redirect to login
			if(Users::authed() === false and in_array(trim($controller, '_controller') . '/' . $action, $public) === false) {
				return Response::redirect('admin/users/login');
			}

			// set template path for admin
			Template::path(PATH . 'system/admin/theme/');
		}
		
		// check we can find a action
		$reflector = new ReflectionClass($controller);
		
		if($reflector->isInstantiable() === false or $reflector->hasMethod($action) === false) {
			// default back to front end template for 404 page
			Template::path(PATH . 'themes/' . $theme . '/');
			
			// method not found in controller
			return Response::error(404);
		}

		$reflector->getMethod($action)->invokeArgs(new $controller, $segments);
	}
	
	private static function parse() {
		// get uri
		$uri = Request::uri();

		// route definitions
		$routes = array();
		
		// posts host page
		if($page = IoC::resolve('postspage')) {
			$routes[$page->slug . '/(:any)'] = 'article/$1';
			$routes[$page->slug] = 'posts';
		}
		
		$admin_folder = Config::get('application.admin_folder');

		// static routes
		$routes = array_merge($routes, array(
			$admin_folder . '/(:any)/(:any)/(:num)' => 'admin/$1/$2/$3',
			$admin_folder . '/(:any)/(:any)' => 'admin/$1/$2',
			$admin_folder . '/(:any)' => 'admin/$1',
			$admin_folder => 'admin',

			'search/(:any)' => 'search/$1',
			'search' => 'search',
			
			'rss' => 'rss',

			'(:any)' => 'page/$1'
		));
		
		// define wild-cards
		$search = array(':any', ':num');
		$replace = array('[0-9a-zA-Z~%\.:_\\-]+', '[0-9]+');
		
		// parse routes
		foreach($routes as $route => $translated) {
			// replace wildcards
			$route = str_replace($search, $replace, $route);

			// look for matches
			if(preg_match('#^' . $route . '#', $uri, $matches)) {
				// replace matched values
				foreach($matches as $k => $match) {
					$translated = str_replace('$' . $k, $match, $translated);
				}

				// return on first match
				return $translated;
			}
		}
		
		return $uri;
	}

}
