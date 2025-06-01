<?php

if( ! defined('ABSPATH') ) exit(); // Exit if accessed directly

use Morilog\Jalali\CalendarUtils;
 
class CRMTicketManager {
    
    private static $instance;
    private $version;
    
    protected static $api_link;

    public static function instance() {
        
        if( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }

    public function __construct() {
        
        $this->version = '1.0.0';
        self::$api_link = DATA_API_LINK;
        
        define("CTM_URI", plugin_dir_url(__DIR__));
        define("CTM_ASSETS_URI", CTM_URI . 'assets/');
        define("CTM_PATH", plugin_dir_path(__DIR__));
        define("CTM_INC_PATH", CTM_PATH . 'inc/');
        define("CTM_ASSETS_PATH", CTM_PATH . 'assets/');
        define("CTM_TEMPLATES_PATH", CTM_PATH . 'templates/');

        $this->init_hooks();
        
        require_once CTM_INC_PATH . 'archive-tickets-manager.php';
        require_once CTM_INC_PATH . 'single-ticket-manager.php';

        ArchiveTicketsHandler::instance();
        SingleTicketsHandler::instance();
    }

    public function init_hooks() {
        add_action('init', array($this, 'set_rerwite_rules'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        add_filter('query_vars', array($this, 'set_query_vars'));
    }

    public function set_rerwite_rules() {
        add_rewrite_tag('%my_tickets%', '([^/]+)');
        add_rewrite_tag('%new_ticket%', '([^/]+)');
        add_rewrite_tag('%ticket_id%', '([a-fA-F0-9-]+)');

        add_rewrite_rule('^my-tickets/?$', 'index.php?my_tickets=1', 'top');
        add_rewrite_rule('^my-tickets/new-ticket/?$', 'index.php?my_tickets=1&new_ticket=1', 'top');
        add_rewrite_rule('^my-tickets/ticket-([a-fA-F0-9-]+)/?$', 'index.php?my_tickets=1&ticket_id=$matches[1]', 'top');
    }

    public function enqueue_assets() {

        if( get_query_var('my_tickets') !== '1' ) return;

        wp_enqueue_style('ctm-global-styles', CTM_ASSETS_URI . 'styles/global.css');

        // sweet alert
        wp_enqueue_script('sweetalert2-scripts', CTM_ASSETS_URI . 'scripts/sweetalert2.all.min.js' , array(), null, true);
        wp_enqueue_style('sweetalert2-styles', CTM_ASSETS_URI . 'styles/sweetalert2.min.css');
    }

    public function set_query_vars($vars) {
        $vars[] = 'my_tickets';
        $vars[] = 'new_ticket';
        $vars[] = 'ticket_id';
        return $vars;
    }

    ////////////////////////////////////////// Helper Functions //////////////////////////////////////////
    protected static function fetch_datas($url, $method = "GET", $body = null, $detailed = false, $prefer = null) {

        $access_token = self::getAccessToken();
        if($access_token === 'failed') return 'failed';
        else if($access_token === false) return false;

        $headers = array(
            'Authorization'=> 'Bearer ' . trim($access_token),
            'OData-MaxVersion'=> '4.0',
            'OData-Version'=> '4.0',
            'Accept'=> 'application/json',
            'Content-Type'=> 'application/json'
        );

        if($prefer !== null) {
            $headers['Prefer'] = $prefer;
        }

        $args = array(
            'method'=> $method,
            'timeout'=> 20,
            'headers'=> $headers
        );

        if($body ==! null) {
            $args['body'] = $body;
        }

        $data = self::handle_req($url, $args, $detailed);
        return $data;
    }

    protected static function getAccessToken() {

        $req_body = array(
            'grant_type'=> 'password',
            'client_id'=> CLIENT_ID,
            'client_secret'=> CLIENT_SECRET,
            'username'=> CLIENT_USERNAME,
            'password'=> CLIENT_PASSWORD,
            'resource'=> self::$api_link
        );

        $args = array(
            'method'=> 'POST',
            'timeout'=> 10,
            'reject_unsafe_urls'=> true,
            'headers'=> 'Content-type: application/x-www-form-urlencoded',
            'body'=> http_build_query($req_body)
        );
        
        $data = self::handle_req(TOKEN_API_LINK, $args);
        return $data === false ? false : ($data->access_token ?? 'failed');

    }

    protected static function handle_req($url, $args, $detailed = false) {

        $response = wp_remote_request($url, $args);

        if(is_wp_error($response)) {
            echo '<h1 style="text-align: center; margin: 200px 0 20px 0;">' . $response->get_error_message() . '</h1>';
            return false;
        }

        $res_code = $response['response']['code'];
        $res_obj = $response['http_response'];

        if($detailed === true) return $response;

        if($res_code === 200) {
            $data = $res_obj->get_data();
            return json_decode($data);
        } else if($res_code === 400) {
            echo '<h1 style="text-align: center; margin: 200px 0 20px 0;">' . $res_header['message'] . '</h1>';
            return false;
        }
    }

    protected static function set_ticket_status(&$ticket) {
        switch ($ticket['status_code']) {
            case '1': $ticket['status']='new'; break;
            case '2': case '4': case '114770001': case '114770000': $ticket['status']='processing'; break;
            case '3': $ticket['status']='answered'; break;
            case '5': case '1000': $ticket['status']='resolved'; break;
            case '6': case '2000': $ticket['status']='canceled'; break;
            default: $ticket['status']='undefined'; break;
        }
        unset($ticket);
    }

    protected static function set_ticket_importance_status(&$ticket) {
        switch ($ticket['importance']) {
            case '3': $ticket['importance_name']='زیاد'; break;
            case '2': $ticket['importance_name']='معمولی'; break;
            case '1': $ticket['importance_name']='کم'; break;
            default: return false; break;
        }
        unset($ticket);
    }

    protected static function set_date_and_time(&$ticket) {
        
        if(isset($ticket['modified']) AND !empty($ticket['modified'])) {
            $modified_on = explode('T', $ticket['modified']);
            $ticket['modified_date'] = $modified_on[0];
            $ticket['modified_time'] = substr($modified_on[1], 0, -1);
            $modified_dates = explode('-', $ticket['modified_date']);
            $ticket['jalali_modified'] = implode('/', CalendarUtils::tojalali($modified_dates[0], $modified_dates[1], $modified_dates[2]));
        }
        
        if(isset($ticket['created']) AND !empty($ticket['created'])) {
            $created_on = explode('T', $ticket['created']);
            $ticket['created_date'] = $created_on[0];
            $ticket['created_time'] = substr($created_on[1], 0, -1);
            $created_dates = explode('-', $ticket['created_date']);
            $ticket['jalali_created'] = implode('/', CalendarUtils::tojalali($created_dates[0], $created_dates[1], $created_dates[2]));
        }
    }

    protected static function set_ticket_modified_status(&$ticket) {
        
        $ticket['modified_status'] = '';
        $modified_date = explode('/', $ticket['jalali_modified']);

        $elapsed_times = self::calc_elapsed_time($modified_date);
        
        $elapsed_years = $elapsed_times['elapsed_years'];
        $elapsed_months = $elapsed_times['elapsed_months'];
        $elapsed_days = $elapsed_times['elapsed_days'];

        if( $elapsed_years !== 0 ) $ticket['modified_status'] .= self::translate_nums($elapsed_years) . ' سال ';
        if( $elapsed_months !== 0 AND $elapsed_years === 0) $ticket['modified_status'] .= self::translate_nums($elapsed_months) . ' ماه ';
        else if( $elapsed_months !== 0 AND $elapsed_years !== 0 ) $ticket['modified_status'] .= 'و ' . self::translate_nums($elapsed_months) . ' ماه ';
        if( $elapsed_days !== 0 AND $elapsed_months === 0 AND $elapsed_years == 0) $ticket['modified_status'] .= self::translate_nums($elapsed_days) . 'روز';
        else if( $elapsed_days !== 0 AND ($elapsed_months !== 0 OR $elapsed_years !== 0) ) $ticket['modified_status'] .= 'و ' .  self::translate_nums($elapsed_days) . ' روز ';
        else if( $elapsed_days === 0 AND $elapsed_months === 0 AND $elapsed_years === 0 ) {
            $ticket['modified_status'] = 'امروز';
            return;
        }

        $ticket['modified_status'] .= ' قبل';
    }

    protected static function translate_nums($expr) {
        return strtr("{$expr}", array('1'=> '۱', '2'=> '۲', '3'=> '۳','4'=> '۴','5'=> '۵','6'=> '۶','7'=> '۷','8'=> '۸','9'=> '۹','0'=> '۰'));
    }

    protected static function calc_elapsed_time($modified_times) {
        $now = explode('/', current_time('Y/m/d'));
        $persian_now = CalendarUtils::Tojalali($now[0], $now[1], $now[2]);

        $elapsed_days = $persian_now[2] - $modified_times[2];
        if($elapsed_days < 0) {
            if($modified_times[1] <= 6) {
                $elapsed_days = 31 + $elapsed_days;
            } else if ($modified_times[1] == 12) {
                if(in_array($modified_times[0] % 33, array(1,5,9,13,17,22,26,30))) {
                    $elapsed_days = 30 + $elapsed_days;
                } else {
                    $elapsed_days = 29 + $elapsed_days;
                }
            } else if($modified_times[1] > 6) {
                $elapsed_days = 30 + $elapsed_days;
            }
            $modified_times[1]++;
        }

        $elapsed_months = $persian_now[1] - $modified_times[1];
        if($elapsed_months < 0) {
            $elapsed_months = 12 + $elapsed_months;
            $modified_times[0]++;
        }

        $elapsed_years = $persian_now[0] - $modified_times[0];

        return array(
            'elapsed_years'=> $elapsed_years,
            'elapsed_months'=> $elapsed_months,
            'elapsed_days'=> $elapsed_days
        );
    }
    
    protected static function disallow_nopriv_users() {
        if(!is_user_logged_in()) {
            wp_redirect(wp_login_url());
            exit();
        }
    }

    protected function get_supports() {

        $my_supports = get_transient('my_supports');
        
        if(!$my_supports) {
            $supporters = self::fetch_datas(self::$api_link . '/systemusers?$filter=new_job eq 100000000&$select=fullname,systemuserid');

            $my_supports = array();
            foreach($supporters->value as $support) $my_supports[$support->systemuserid] = $support->fullname;
            set_transient('my_supports', $my_supports, WEEK_IN_SECONDS);
        }

        return $my_supports;
    }

    protected static function structure_ticket($ticket, $supports) {
        
        $item = array(
            'id'=> $ticket['incidentid'],
            'ticket_id'=> $ticket['ticketnumber'],
            'title'=> $ticket['title'],
            'importance'=> 4 - intval($ticket['prioritycode']),
            'created'=> $ticket['createdon'],
            'modified'=> $ticket['modifiedon'],
            'status_code'=> $ticket['statuscode'], 
            'support'=> $supports[$ticket['_ownerid_value']] ?? 'تعریف نشده',
            'support_id'=> !$ticket['new_supportid'] ? '1' : $ticket['new_supportid'],
            'is_read'=> ($ticket['new_isread'] === false) ? 'false' : 'true'
        );

        return $item;

    }
}   