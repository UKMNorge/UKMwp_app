<?php
    
class UKMwpAPI {

    public static function registerEndpoints() {
        /**
         * ENDPOINT 3: Sider i informasjonsmenyen
         */
        $register = register_rest_route(
			'UKM', 
			'/informasjon/', 
			[
				'methods' => 'GET',
				'callback' => ['UKMwpAPI', 'informasjonsMeny'],
				'args' => []
			]
        );

        /**
         * ENDPOINT 4: Innlegg i bloggen
         */
        $register = register_rest_route(
			'UKM', 
			'/nyheter/', 
			[
				'methods' => 'GET',
				'callback' => ['UKMwpAPI', 'nyheter'],
				'args' => []
			]
        );


        /**
         * ENDPOINT 5: Innlegg fra kategori
         */
        $register = register_rest_route(
			'UKM', 
			'/kategori/(?P<id>\d+)', 
			[
				'methods' => 'GET',
				'callback' => ['UKMwpAPI', 'kategori'],
				'args' => []
			]
        );

        /**
         * ENDPOINT 1 Info om en gitt post
         */
        $register = register_rest_route(
            'UKM',
            '/post/(?P<id>\d+)',
            [
                'methods' => 'GET',
				'callback' => ['UKMwpAPI', 'post'],
                'args' => ['id']
            ]
        );

        /**
         * ENDPOINT 2: Innholdet i en post
         */
        $register = register_rest_route(
            'UKM',
            '/content/(?P<id>\d+)',
            [
                'methods' => 'GET',
				'callback' => ['UKMwpAPI', 'postContent'],
                'args' => ['id']
            ]
        );
    }


    /**
     * ENDPOINT 1: Info om en gitt post
     * /UKM/post/$id
     */
    public static function post( $request ) {
        $postData = self::_getPostFromId( $request->get_param('id') );

        $data = self::_getPostDataFromWPOOPost( $postData );
        return $data;
    }

    /**
     * ENDPOINT 2: Innholdet i en post
     * /UKM/content/$id
     */
    public static function postContent( $request ) {
        $postData = self::_getPostFromId( $request->get_param('id') );

        $data = new stdClass();
        $data->id       = $postData->ID;
        $data->lead     = $postData->lead;
        $data->content  = $postData->content_wo_lead;
        
        return $data;
    }

    /**
     * ENDPOINT 3: Sider i informasjonsmenyen
     * /UKM/informasjon
     */
    public static function informasjonsMeny() {
        if( !get_option('UKM_menu') ) {
            return 'false';
        }

        $menu = wp_get_nav_menu_object( get_option('UKM_menu') );
        $menu_items = wp_get_nav_menu_items( $menu );

        $items = [];
        foreach( $menu_items as $menu_item ) {
            $postData = self::_getPostFromId( $menu_item->object_id );
            $items[] = self::_getPostDataFromWPOOPost( $postData );
        }

        return $items;
    }

    /**
     * ENDPOINT 4: Innlegg i bloggen
     */
    public static function nyheter() {
        $nyheter = [];
        $posts = query_posts('posts_per_page=100');
        global $post;
	    while( have_posts() ) {
	       the_post();
	       $nyheter[] = self::_getPostDataFromWPOOPost( new WPOO_Post( $post ) );
        }
        
        return $nyheter;
    }


    /**
     * ENDPOINT 5: Innlegg fra gitt kategori
     */
    public static function kategori( $request ) {
        $innlegg = [];
        $posts = query_posts('posts_per_page=100&cat='. (int)$request->get_param('id') );
        global $post;
	    while( have_posts() ) {
	       the_post();
	       $innlegg[] = self::_getPostDataFromWPOOPost( new WPOO_Post( $post ) );
        }
        
        return $innlegg;
    }

    /**
     * HELPER: Hent gitt post fra ID
     */
    public static function _getPostFromId( $_post_id ) {
        global $post_id;
        $post_id = $_post_id;
        $post = get_post( $post_id );
        setup_postdata( $post );
        return new WPOO_Post( $post );
    }

    /**
     * HELPER: Setup return post data fra WPOO-objekt
     */
    public static function _getPostDataFromWPOOPost( $wpoo_post ) {
        
        $data = new stdClass();

        $data->id           = $wpoo_post->ID;
        $data->date         = $wpoo_post->raw->date;
        $data->title        = $wpoo_post->title;
        $data->lead         = $wpoo_post->lead;
        $data->url          = $wpoo_post->url;
        $data->image        = $wpoo_post->image->url;//'https://placehold.it/300x169/';
        $data->contenturl   = 'https://ukm.no/testfylke/wp-json/UKM/content/'. $data->id;

        return $data;
    }
}
