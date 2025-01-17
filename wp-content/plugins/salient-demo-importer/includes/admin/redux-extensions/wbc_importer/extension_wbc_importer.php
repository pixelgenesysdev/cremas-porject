<?php
/**
 * Extension-Boilerplate
 *
 * @link https://github.com/ReduxFramework/extension-boilerplate
 *
 * Radium Importer - Modified For ReduxFramework
 * @link https://github.com/FrankM1/radium-one-click-demo-install
 *
 * @package     WBC_Importer - Extension for Importing demo content
 * @author      Webcreations907
 * @version     1.0.3
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// Don't duplicate me!
if ( !class_exists( 'ReduxFramework_extension_wbc_importer' ) ) {

    class ReduxFramework_extension_wbc_importer {

        public static $instance;

        static $version = "1.0.3";

        protected $parent;

        private $filesystem = array();

        public $extension_url;

        public $extension_dir;

        public $demo_data_dir;

        public $wbc_import_files = array();

        public $active_import_id;

        public $active_import;

        public $field_name;

        public $nectar_import_demo_content;
        public $nectar_import_theme_option_settings;
        public $nectar_import_demo_widgets;
        
        // Nectar addition
        public $demo_order = array(
          'Portfolio-Layered'     => 11,
          'Portfolio-Quantum'     => 12,
          'Mag'                   => 13,
          'SaaS'                  => 14,
          'Resort'                => 15,
          'Architect'             => 16,
          'Ecommerce-Robust'      => 17,
          'Wellness'              => 18,
          'Nonprofit'             => 19,
          'Business-3'            => 20,
          'Corporate-3'           => 21,
          'Freelance-Portfolio'   => 22,
          'Ecommerce-Ultimate'    => 23,
          'Ecommerce-Creative'    => 24,
          'Dark-Blog'             => 25,
          'Corporate-2'           => 26,
          'Blog-Ultimate'         => 27,
          'Corporate-Creative'    => 28,
          'Blog-Magazine'         => 29,
          'Business-2'            => 30,
          'Company-Startup'       => 31,
          'Fullscreen Portfolio Slider' => 32,
          'Band'                  => 33,
          'Minimal Portfolio'     => 34,
          'Corporate'             => 35,
          'Agency'                => 36,
          'Restaurant'            => 37,
          'Business'              => 38,
          'Landing Service'       => 39,
          'Photography'           => 40,
          'Landing Product'       => 41,
          'App'                   => 42,
          'Simple Blog'           => 43,
          'Old-School-Ecommerce'  => 44,
          'One-Page'              => 45,
          'Ascend'                => 46,
          'Frostwave'              => 47,
          'Old-School-All-Purpose' => 48,
        );

        /**
         * Class Constructor
         *
         * @since       1.0
         * @access      public
         * @return      void
         */
        public function __construct( $parent ) {

            $this->parent = $parent;

            if ( !is_admin() ) {
              return;
            }
            
            // do not load anywhere except needed 
            if ( isset($_GET['page']) && $_GET['page'] == $this->parent->args['page_slug'] || isset($_REQUEST['demo_import_id']) || isset($_REQUEST['plugin_src']) ) { 
              //init
            } else {
              //still add menu item
              $this->add_importer_section();
              return; 
            }
            
            //plugin installer
            if(file_exists( dirname( __FILE__ ) . '/wbc_importer/connekt-plugin-installer/class-connekt-plugin-installer.php' ) ) {
              include_once('wbc_importer/connekt-plugin-installer/class-connekt-plugin-installer.php');
            }

            //Hides importer section if anything but true returned. Way to abort :)
            if ( true !== apply_filters( 'wbc_importer_abort', true ) ) {
                return;
            }
            

            if ( empty( $this->extension_dir ) ) {
                $this->extension_dir = SALIENT_DEMO_IMPORTER_ROOT_DIR_PATH . 'includes/admin/redux-extensions/wbc_importer/';
                $this->extension_url = site_url( str_replace( trailingslashit( str_replace( '\\', '/', ABSPATH ) ), '', $this->extension_dir ) );
                $this->demo_data_dir = apply_filters( "wbc_importer_dir_path", $this->extension_dir . 'demo-data/' );
            }

            //Delete saved options of imported demos, for dev/testing purpose
            // delete_option('wbc_imported_demos');

            $this->getImports();

            $this->field_name = 'wbc_importer';
            
            self::$instance = $this;

            add_filter( 'redux/' . $this->parent->args['opt_name'] . '/field/class/' . $this->field_name, array( &$this,
                    'overload_field_path'
                ) );

            add_action( 'wp_ajax_redux_wbc_importer', array(
                    $this,
                    'ajax_importer'
                ) );

            add_filter( 'redux/'.$this->parent->args['opt_name'].'/field/wbc_importer_files', array(
                    $this,
                    'addImportFiles'
                ) );

            //Adds Importer section to panel
            $this->add_importer_section();

            include $this->extension_dir.'inc/class-wbc-importer-progress.php';
            $wbc_progress = new Wbc_Importer_Progress( $this->parent );
        }


        
        /**
         * Get the demo folders/files
         * Provided fallback where some host require FTP info
         *
         * @return array list of files for demos
         */
        
        public static function is_writable() {

            global $wp_filesystem;
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
            
            $wp_upload_dir = wp_upload_dir( null, false );
            $upload_dir = $wp_upload_dir['basedir'];
        
            if ( ! function_exists( 'WP_Filesystem' ) ) {
              return false;
            }
    
            WP_Filesystem();
        
            $writable = WP_Filesystem( false, $upload_dir );
        
            return ( $writable && 'direct' === $wp_filesystem->method );
        }

        public function demoFiles() {
            
            $this->filesystem = $this->parent->filesystem->execute( 'object' );
            
            if( self::is_writable() ) {
        
                $dir_array = $this->filesystem->dirlist( $this->demo_data_dir, false, true );

                if ( !empty( $dir_array ) && is_array( $dir_array ) ) {
                   
                    uksort( $dir_array, 'strcasecmp' );
                    return $dir_array;
                }
            }
           

            $dir_array = array();

            $demo_directory = array_diff( scandir( $this->demo_data_dir ), array( '..', '.' ) );

            if ( !empty( $demo_directory ) && is_array( $demo_directory ) ) {
                foreach ( $demo_directory as $key => $value ) {
                    if ( is_dir( $this->demo_data_dir.$value ) ) {

                        $dir_array[$value] = array( 'name' => $value, 'type' => 'd', 'files'=> array() );

                        $demo_content = array_diff( scandir( $this->demo_data_dir.$value ), array( '..', '.' ) );

                        foreach ( $demo_content as $d_key => $d_value ) {
                            if ( is_file( $this->demo_data_dir.$value.'/'.$d_value ) ) {
                                $dir_array[$value]['files'][$d_value] = array( 'name'=> $d_value, 'type' => 'f' );
                            }
                        }
                    }
                }

                uksort( $dir_array, 'strcasecmp' );
            }
            
            return $dir_array;
        }


        public function getImports() {

            if ( !empty( $this->wbc_import_files ) ) {
                return $this->wbc_import_files;
            }

            $imports = $this->demoFiles();

            $imported = get_option( 'wbc_imported_demos' );

            if ( !empty( $imports ) && is_array( $imports ) ) {
                $x = 1;
                foreach ( $imports as $import ) {

                    if ( !isset( $import['files'] ) || empty( $import['files'] ) ) {
                        continue;
                    }

                    if ( $import['type'] == "d" && !empty( $import['name'] ) ) {
                        $this->wbc_import_files['wbc-import-'.$x] = isset( $this->wbc_import_files['wbc-import-'.$x] ) ? $this->wbc_import_files['wbc-import-'.$x] : array();
                        $this->wbc_import_files['wbc-import-'.$x]['directory'] = $import['name'];

                        if ( !empty( $imported ) && is_array( $imported ) ) {
                            if ( array_key_exists( 'wbc-import-'.$x, $imported ) ) {
                                $this->wbc_import_files['wbc-import-'.$x]['imported'] = 'imported';
                            }
                        }
                        
                        //Nectar addition add in order
                        if( isset( $this->demo_order[$this->wbc_import_files['wbc-import-'.$x]['directory']] ) ) {
                          $this->wbc_import_files['wbc-import-'.$x]['order'] = $this->demo_order[$this->wbc_import_files['wbc-import-'.$x]['directory']];
                        }

                        foreach ( $import['files'] as $file ) {
                            switch ( $file['name'] ) {
                            case 'content.xml':
                                $this->wbc_import_files['wbc-import-'.$x]['content_file'] = $file['name'];
                                break;

                            case 'theme-options.txt':
                            case 'theme-options.json':
                                $this->wbc_import_files['wbc-import-'.$x]['theme_options'] = $file['name'];
                                break;

                            case 'widgets.json':
                            case 'widgets.txt':
                                $this->wbc_import_files['wbc-import-'.$x]['widgets'] = $file['name'];
                                break;

                            case 'screen-image.png':
                            case 'screen-image.jpg':
                            case 'screen-image.gif':
                                $this->wbc_import_files['wbc-import-'.$x]['image'] = $file['name'];
                                break;
                            }

                        }

                    }

                    $x++;
                }

            }

        }

        public function addImportFiles( $wbc_import_files ) {

            if ( !is_array( $wbc_import_files ) || empty( $wbc_import_files ) ) {
                $wbc_import_files = array();
            }

            $wbc_import_files = wp_parse_args( $wbc_import_files, $this->wbc_import_files );

            return $wbc_import_files;
        }

        public function ajax_importer() {
            if ( !isset( $_REQUEST['nonce'] ) || !wp_verify_nonce( $_REQUEST['nonce'], "redux_{$this->parent->args['opt_name']}_wbc_importer" ) ) {
                die( 0 );
            }
            if ( isset( $_REQUEST['type'] ) && $_REQUEST['type'] == "import-demo-content" && array_key_exists( $_REQUEST['demo_import_id'], $this->wbc_import_files ) ) {

                $reimporting = false;

                if ( isset( $_REQUEST['wbc_import'] ) && $_REQUEST['wbc_import'] == 're-importing' ) {
                    $reimporting = true;
                }

                $this->active_import_id = $_REQUEST['demo_import_id'];
                
                $this->active_import = array( $this->active_import_id => $this->wbc_import_files[$this->active_import_id] );
                
                $this->nectar_import_demo_content = ( isset($_REQUEST['import_demo_content']) ) ? $_REQUEST['import_demo_content'] : 'true';
                $this->nectar_import_theme_option_settings = ( isset($_REQUEST['import_theme_option_settings']) ) ? $_REQUEST['import_theme_option_settings'] : 'true';
                $this->nectar_import_demo_widgets = ( isset($_REQUEST['import_demo_widgets']) ) ? $_REQUEST['import_demo_widgets'] : 'true';
                
                if ( !isset( $import_parts['imported'] ) || true === $reimporting ) {
                    include $this->extension_dir.'inc/init-installer.php';
                    $installer = new Radium_Theme_Demo_Data_Importer( $this, $this->parent );
                }else {
                    echo esc_html__( "Demo Already Imported", 'salient-demo-importer' );
                }

                die();
            }

            die();
        }

        public static function get_instance() {
            return self::$instance;
        }

        // Forces the use of the embeded field path vs what the core typically would use
        public function overload_field_path( $field ) {
            return dirname( __FILE__ ) . '/' . $this->field_name . '/field_' . $this->field_name . '.php';
        }

        function add_importer_section() {
            // Checks to see if section was set in config of redux.
            for ( $n = 0; $n <= count( $this->parent->sections ); $n++ ) {
                if ( isset( $this->parent->sections[$n]['id'] ) && $this->parent->sections[$n]['id'] == 'wbc_importer_section' ) {
                    return;
                }
            }

            $wbc_importer_label = trim( esc_html( apply_filters( 'wbc_importer_label', __( 'Demo Importer', 'salient-demo-importer' ) ) ) );

            $wbc_importer_label = ( !empty( $wbc_importer_label ) ) ? $wbc_importer_label : __( 'Demo Importer', 'salient-demo-importer' );

            $this->parent->sections[] = array(
                'id'     => 'wbc_importer_section',
                'title'  => $wbc_importer_label,
                'desc'   => '<p class="description">'. apply_filters( 'wbc_importer_description', esc_html__( 'Works best to import on a new install of WordPress.  If you\'re not using a fresh install, make sure you backup your current theme options as they will be overwritten.', 'salient-demo-importer' ) ).'</p>',
                'icon'   => 'el-icon-website',
                'fields' => array(
                    array(
                        'id'   => 'wbc_demo_importer',
                        'type' => 'wbc_importer'
                    )
                )
            );
        }

    } // class
} // if
