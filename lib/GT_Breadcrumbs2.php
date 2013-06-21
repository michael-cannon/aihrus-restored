<?php
/**
 * Allows breadcrumb trails to be added
 *
 * @package GT_Breadcrumbs_2
 * @author Gary Jones
 * @since 2010-04-20
 */
class GT_Breadcrumbs extends GT_Model_Entity {
	protected $_data			= null;

	public function __construct(array $data = null) {
		parent::__construct($data);

		$this->_data			= array(
			'archiveText'		=> __('Archive', 'custom'),
			'authorText'		=> __('Author', 'custom'),
			'categoryText'		=> __('Category', 'custom'),
			'notFoundText'		=> __('Not Found', 'custom'),
			'rootText'			=> __('Home', 'custom'),
			'searchText'		=> __('Search', 'custom'),
			'separator'			=> __('/', 'custom'),
			'showOnHomePage'	=> false,
			'tagText'			=> __('Tag', 'custom'),
			'wrapperClass'		=> 'breadcrumbs'
		);
	}

	/**
	 * Output the breadcrumbs by attaching it to the supplied action hook
	 *
	 * @param string $hook Name of the action hook to attach it to
	 * @param int $priority Determines the order of execution within the action hook (10 is default)
	 */
	public function hook($hook, $priority = 10) {
		if ( !empty( $hook ) ) {
			add_action( $hook, array( &$this, 'build' ), $priority );
		} else {
			throw new Exception('The "hook" method must contain the name of an action hook. e.g. $breadcrumbs->hook(\'hook_name_here\');');
		}
		return $this;
	}

	/**
	 * Ensures all crumbs are prefixed by the separator
	 * @param mixed $crumb
	 */
	private function _addCrumb($crumb) {
		return ' ' . $this->separator . ' ' . $crumb;
	}

	/**
	 * This does the main work to put the breadcrumbs together
	 */
	public function build() {
		global $post;
		$output = '<div class="' . $this->wrapperClass . '"><a title="' . get_bloginfo('description') . '" href="' . get_option('home') . '">' . $this->rootText . '</a>';
		if( is_single() && ! is_attachment() ) {
			$output .= $this->_addCrumb('');
			foreach( get_the_category() as $category ) {
				$output .= '<a href="' . get_category_link($category->cat_ID) . '">' . $category->cat_name . '</a>, ';
			}
			$output = substr( $output, 0, strlen($output)-2 ); /* Strips comma and space from last category */
			$output .= $this->_addCrumb( get_the_title() );
		} elseif( is_page() || is_attachment() ) {
			$ancestors = $this->_get_ancestor_ids($post->ID, false);
			foreach ($ancestors as $ancestor) {
				$page = get_page($ancestor);
				if ( $post->ID == $page->ID )
					break;
				$output .= $this->_addCrumb('<a title="' . $page->post_title . '" href="' . get_page_link( $page->ID ) . '">' . $page->post_title . '</a>');
			}
			$output .= $this->_addCrumb( get_the_title() );
		} elseif( is_tag() ) {
			$output .= $this->_addCrumb( $this->tagText ) . $this->_addCrumb( single_tag_title('', false) );
		} elseif( is_category() ) {
			$output .= $this->_addCrumb( $this->categoryText ) . $this->_addCrumb( single_cat_title('', false) );
		} elseif( is_month() ) {
			$output .= $this->_addCrumb( $this->archiveText ) . $this->_addCrumb( get_the_time('F Y') );
		} elseif( is_year() ) {
			$output .= $this->_addCrumb( $this->archiveText ) . $this->_addCrumb( get_the_time('Y') );
		} elseif( is_author() ) {
			$output .= $this->_addCrumb( $this->authorText ) . $this->_addCrumb( get_author_name(get_query_var('author')) );
		} elseif( is_search() ) {
			$output .= $this->_addCrumb( $this->searchText ) . $this->_addCrumb( get_search_query() );
		} elseif( is_404() ) {
			$output .= $this->_addCrumb( $this->notFoundText );
		}
		$output .= '</div>';
		if( (is_home() || is_front_page()) && !$this->showOnHomePage ) {
			$output = ''; /* Wipe output clean */
		}
		echo $output;
	}

	/** get_parent_id
	 * get the id of the parent of a given page
	 * @author http://ethancodes.com/2008/09/get-grandparent-pages-in-wordpress/
	 * @param int page id
	 * @return int the id of the page's parent page
	 */
	private function _get_parent_id ( $child = 0 ) {
		global $wpdb;
		// Make sure there is a child ID to process
		if ( $child > 0 ) {
			$result = $wpdb->get_var("SELECT post_parent FROM $wpdb->posts WHERE ID = $child");
		} else {
			// ... or set a zero result.
			$result = 0;
		}
		//
		return $result;
	}

	/** get_ancestor_ids
	 * get an array of ancestor ids for a given page
	 * you get an array that looks something like
	 * [0] this page id
	 * [1] parent page id
	 * [2] grandparent page id
	 * @author http://ethancodes.com/2008/09/get-grandparent-pages-in-wordpress/
	 * @param int page you want the ancestry of
	 * @param boolean include this page in the tree (optional, default true)
	 * @param boolean results top down (optional, default true)
	 * @return an array of ancestor ids
	 */
	function _get_ancestor_ids ( $child = 0, $inclusive=true, $topdown=true ) {
		if ( $child && $inclusive ) $ancestors[] = $child;
		while ($parent = $this->_get_parent_id ( $child ) ) {
			$ancestors[] = $parent;
			$child = $parent;
		}
		//      If there are ancestors, test for resorting, and apply
		if ($ancestors && $topdown) krsort($ancestors);
		if ( !$ancestors ) $ancestors[] = 0;
		//
		return $ancestors;
	}
}

class GT_Model_Entity
{

	public function __construct(array $data = null)
	{
		if (!is_null($data)) {
			foreach ($data as $name => $value) {
				$this->{$name} = $value;
			}
		}
	}

	public function toArray()
	{
		return $this->_data;
	}

	public function __set($name, $value)
	{
		if (!array_key_exists($name, $this->_data)) {
			throw new Exception('You can not set new properties on this object');
		}
		$this->_data[$name] = $value;
	}

	public function __get($name)
	{
		if (array_key_exists($name, $this->_data)) {
			return $this->_data[$name];
		}
	}

	public function __isset($name)
	{
		return isset($this->_data[$name]);
	}

	public function __unset($name)
	{
		if (isset($this->_data[$name])) {
			unset($this->_data[$name]);
		}
	}

}

?>