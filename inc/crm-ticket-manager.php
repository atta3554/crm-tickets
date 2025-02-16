<?php

if( ! defined('ABSPATH') ) exit(); // Exit if accessed directly

use Morilog\Jalali\CalendarUtils;

class CRMTicketManager {
    
    private static $instance;
    private $version;
    private $api_link;
    private $filters;
    private $tickets_statuses;

    public static function instance() {
        
        if( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }

    public function __construct() {
        
        $this->version = '1.0.0';
        $this->api_link = 'https://run.mocky.io/v3/b65183aa-cf5d-4cf2-a1a9-31f4d5c9191d?';

        $this->tickets_statuses = array(
            'all'=> array(
                'status'=> 'همه',
                'count'=> 0,
            ),
            'open'=> array(
                'status'=> 'باز',
                'count'=> 0,
            ),
            'closed'=> array(
                'status'=> 'بسته',
                'count'=> 0,
            ),
            'answered'=> array(
                'status'=> 'پاسخ داده شده',
                'count'=> 0,
            ),
            'finished'=> array(
                'status'=> 'پایان یافته',
                'count'=> 0,
            ),
            'canceled'=> array(
                'status'=> 'لغو شده',
                'count'=> 0,
            ),
            'processing'=> array(
                'status'=> 'در حال انجام',
                'count'=> 0,
            )
        );

        $this->filters = array(
            "by_read"=> array(
                "all"=>array(
                    "status"=> "همه",
                    "count"=> 0
                ),
                "is_read"=> array(
                    "status"=> "خوانده شده",
                    "count"=> 0
                ),
                "is_unread"=> array(
                    "status"=> "خوانده نشده",
                    "count"=> 0
                )
            ),
            "by_status"=> &$this->tickets_statuses,
            "by_date"=> array(
                "creation_date"=> array(
                    "status"=>'تاریخ ایجاد',
                    'filtered'=> false
                ),
                "modified_date"=> array(
                    "status"=>'تاریخ پاسخ',
                    'filtered'=> false
                )
            ),
        );
        
        define("CTM_URI", plugin_dir_url(__DIR__));
        define("CTM_ASSETS_URI", CTM_URI . 'assets/');
        define("CTM_PATH", plugin_dir_path(__DIR__));
        define("CTM_INC_PATH", CTM_PATH . 'inc');
        define("CTM_ASSETS_PATH", CTM_PATH . 'assets');
        define("CTM_TEMPLATES_PATH", CTM_PATH . 'templates');

        $this->init_hooks();
    }

    public function init_hooks() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        add_action('init', array($this, 'set_rerwite_rules'));
        add_filter('query_vars', array($this, 'set_query_vars'));
        add_action('template_redirect', array($this, 'handle_ticket_pages'));
    }

    public function enqueue_assets() {
        wp_enqueue_style('archive-tickets-fonts', CTM_ASSETS_URI . '/styles/fonts.css');

        if(get_query_var('my_tickets') == '1' AND !get_query_var('ticket_id')) {
            wp_enqueue_style('archive-tickets-styles', CTM_ASSETS_URI . '/styles/archive-tickets.css');
        }
    }

    public function set_rerwite_rules() {
        add_rewrite_tag('%my_tickets%', '([^/]+)');
        add_rewrite_tag('%ticket_id%', '(\d+)');

        add_rewrite_rule('^my-tickets/?$', 'index.php?my_tickets=1', 'top');
        add_rewrite_rule('^my-tickets/ticket-(\d+)/?$', 'index.php?my_tickets=1&ticket_id=$matches[1]', 'top');
    }

    public function set_query_vars($vars) {
        $vars[] = 'my_tickets';
        $vars[] = 'ticket_id';
        return $vars;
    }

    public function handle_ticket_pages() {
        global $wp_query;

        if( get_query_var('my_tickets') == '1' AND get_query_var('ticket_id') ) $this->render_single_ticket();

        else if(get_query_var('my_tickets') == '1') $this->render_archive_tickets();
    }

    ////////////////////// Not Finished
    public function render_single_ticket() {
        $ticket_id = get_query_var('ticket_id');
        require_once CTM_TEMPLATES_PATH . '/single-ticket-template.php';
    }
    ///////////////////// Here Will Finishes

    public function render_archive_tickets() {
        $user_id = get_current_user_id();
        $url = $this->api_link . 'user=' . $user_id;
        $fetched_tickets = $this->fetch_tickets($url);
        $this->reintegrate_tickets($fetched_tickets);
        require_once CTM_TEMPLATES_PATH . '/my-tickets-template.php';
    }

    public function fetch_tickets($url) {

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);

        return $data;
    }

    public function reintegrate_tickets(&$tickets) {

        if(isset($tickets) AND !empty($tickets)) {
            foreach($tickets as &$ticket) {
                $this->set_ticket_status($ticket);
                $this->create_filters($ticket);
                $this->set_ticket_importance_status($ticket);
                $this->set_ticket_modified_status($ticket);
            }
        }

        unset($ticket);

        foreach($this->tickets_statuses as &$ticket) $ticket['count'] = $this->translate_nums($ticket['count']);

        unset($ticket);
        unset($tickets);
    }

    public function set_ticket_status($ticket) {
        $this->tickets_statuses[$ticket['status']]['count']++;
        $this->tickets_statuses['all']['count']++;
    }

    public function create_filters($ticket) {
        if($ticket['is_read']) $this->filters['by_read']['is_read']['count']++;
        else $this->filters['by_read']['is_unread']['count']++;
        $this->filters['by_read']['all']['count']++;
    }

    public function set_ticket_importance_status(&$ticket) {
        switch ($ticket['importance']) {
            case '3': $ticket['importance_name']='زیاد'; break;
            case '2': $ticket['importance_name']='معمولی'; break;
            case '1': $ticket['importance_name']='کم'; break;
            default: return false; break;
        }
        unset($ticket);
    }

    public function set_ticket_modified_status(&$ticket) {
        
        $ticket['modified_status'] = '';
        
        $ticket_modified = explode('/', $ticket['modified']);

        $elapsed_times = $this->calc_elapsed_time($ticket_modified);
        
        $elapsed_years = $elapsed_times['elapsed_years'];
        $elapsed_months = $elapsed_times['elapsed_months'];
        $elapsed_days = $elapsed_times['elapsed_days'];


        if( $elapsed_years !== 0 ) $ticket['modified_status'] .= $this->translate_nums($elapsed_years) . ' سال ';
        if( $elapsed_months !== 0 AND $elapsed_years === 0) $ticket['modified_status'] .= $this->translate_nums($elapsed_months) . ' ماه ';
        else if( $elapsed_months !== 0 AND $elapsed_years !== 0 ) $ticket['modified_status'] .= 'و ' . $this->translate_nums($elapsed_months) . ' ماه ';
        if( $elapsed_days !== 0 AND $elapsed_months === 0 AND $elapsed_years == 0) $ticket['modified_status'] .= $this->translate_nums($elapsed_days) . 'روز';
        else if( $elapsed_days !== 0 AND ($elapsed_months !== 0 OR $elapsed_years !== 0) ) $ticket['modified_status'] .= 'و ' .  $this->translate_nums($elapsed_days) . ' روز ';

        $ticket['modified_status'] .= ' قبل';
        // unset($ticket);
    }

    public function translate_nums($num) {
        return strtr("{$num}", array('1'=> '۱', '2'=> '۲', '3'=> '۳','4'=> '۴','5'=> '۵','6'=> '۶','7'=> '۷','8'=> '۸','9'=> '۹','0'=> '۰'));
    }

    public function calc_elapsed_time($modified_times) {

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
            $modified_times[1]--;
        }
    
        $elapsed_months = $persian_now[1] - $modified_times[1];
        if($elapsed_months < 0) {
            $elapsed_months = 12 + $elapsed_months;
            $modified_times[0]--;
        }

        $elapsed_years = $persian_now[0] - $modified_times[0];

        return array(
            'elapsed_years'=> $elapsed_years,
            'elapsed_months'=> $elapsed_months,
            'elapsed_days'=> $elapsed_days
        );
    }
}