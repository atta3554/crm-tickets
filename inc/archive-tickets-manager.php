<?php

if(!defined('ABSPATH')) exit();

class ArchiveTicketsHandler extends CRMTicketManager {

    private static $instance;
    private $filters;
    private $user_tickets;
    private $tickets_per_page;
    public $tickets_statuses;

    public static function instance() {
        
        if( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct() {

        $this->tickets_per_page = 10;

        $this->tickets_statuses = array(
            'all'=> array(
                'status'=> 'همه',
                'count'=> 0,
            ),
            'new'=> array(
                'status'=> 'جدید',
                'count'=> 0,
            ),
            'resolved'=> array(
                'status'=> 'حل شده',
                'count'=> 0,
            ),
            'canceled'=> array(
                'status'=> 'لغو شده',
                'count'=> 0,
            ),
            'answered'=> array(
                'status'=> 'پاسخ داده شده',
                'count'=> 0,
            ),
            'processing'=> array(
                'status'=> 'در حال بررسی',
                'count'=> 0,
            ),
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

        $this->init_hooks();
    }

    public function init_hooks() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_archive_assets'));
        add_action('template_redirect', array($this, 'render_archive_tickets'));
        add_action('wp_ajax_sort_tickets', array($this, 'sort_tickets'));
        add_action('wp_ajax_nopriv_sort_tickets', array($this, 'sort_tickets'));
        add_action('wp_ajax_page_tickets', array($this, 'page_tickets'));
        add_action('wp_ajax_nopriv_page_tickets', array($this, 'page_tickets'));
    }

    public function enqueue_archive_assets() {

        if( get_query_var('new_ticket') == '1' OR get_query_var('ticket_id') OR get_query_var('my_tickets') !== '1' ) return;

        $style_path = CTM_ASSETS_PATH . 'styles/archive.css';
        $script_path = CTM_ASSETS_PATH . 'scripts/archive.js';

        $archive_data = array(
            'ajax_url'=> admin_url('admin-ajax.php'),
            'tickets_pagination_nonce'=> wp_create_nonce('tickets_pagination_nonce'),
        );

        wp_enqueue_style('archive-tickets-styles', CTM_ASSETS_URI . 'styles/archive.css', NULL, filemtime($style_path));
        wp_enqueue_script('archive-tickets-scripts', CTM_ASSETS_URI . 'scripts/archive.js', array('jquery'), filemtime($script_path), true);
        wp_localize_script('archive-tickets-scripts', 'archive_data', $archive_data);

    }
    
    public function render_archive_tickets() {
        
        parent::disallow_nopriv_users();

        if( get_query_var('new_ticket') == '1' OR get_query_var('ticket_id') OR get_query_var('my_tickets') !== '1' ) return;
        
        $account_id = $this->get_user_accounts();

        if($account_id === false) return;

        $this->user_tickets = get_transient("createdon_sorted_tickets_$account_id") === false ? array() : get_transient("createdon_sorted_tickets_$account_id");
        if(!empty($this->user_tickets) AND count($this->user_tickets) > 0) {
            
            $this->set_tickets_metadata();

            $ticket_pages = $this->create_pagination();

            require_once CTM_TEMPLATES_PATH . 'my-tickets-template.php';
            return;
        }

        $fetched_tickets = parent::fetch_datas(parent::$api_link . '/incidents?$select=incidentid,ticketnumber,title,prioritycode,createdon,modifiedon,statuscode,_ownerid_value,new_supportid,new_isread&$filter=_customerid_value eq ' . $account_id . '&$orderby=createdon desc');
        $fetched_tickets = json_decode(json_encode($fetched_tickets),true);

        if($fetched_tickets === false) die('<h1 style="text-align: center; margin-bottom: 200px;">Something went Wrong! please try again later.</h1>');
        else if($fetched_tickets === NULL) die('<h1 style="text-align: center; margin: 200px 0;">Failed to get Data! please try again later.</h1>');
        else if($fetched_tickets === 'failed') die('<h1 style="text-align: center; margin: 200px 0;">Failed to get AccessToken! please try again later.</h1>');

        $my_supports = parent::get_supports();
        foreach($fetched_tickets['value'] as $ticket) $this->user_tickets[] = parent::structure_ticket($ticket, $my_supports);
        $this->reintegrate_tickets();
        $this->set_tickets_metadata();

        $ticket_pages = $this->create_pagination();
         

        require_once CTM_TEMPLATES_PATH . 'my-tickets-template.php';
        
        set_transient("createdon_sorted_tickets_$account_id", $this->user_tickets, HOUR_IN_SECONDS) ? '<h1>successfully</h1>' : '<h1>error</h1>';
        unset($fetched_tickets);
        gc_collect_cycles();
    }

    public function get_user_accounts() {

        $user_id = get_current_user_id();
        $user_phone = get_user_meta($user_id, 'phone_number', true);
        $contact_datas = parent::fetch_datas(parent::$api_link . "/contacts?\$select=contactid&\$filter=mobilephone eq '$user_phone'");

        if(empty($contact_datas) OR !isset($contact_datas->value) OR count($contact_datas->value) === 0) die('<h1>اطلاعات شما یافت نشد! لطفا از شماره تلفن معتبر استفاده کنید.</h1>');
        
        $contact_ids = array_map(fn($contact) => $contact->contactid, $contact_datas->value);
        
        if(empty($contact_ids) OR count($contact_ids) === 0) die('<h1>اطلاعات شما یافت نشد! لطفا از اشخاص معتبر استفاده کنید.</h1>');

        $account_infos = array();
        foreach($contact_ids as $id) {
            $account_datas = parent::fetch_datas(parent::$api_link . "/accounts?\$select=accountid,name&\$filter=_primarycontactid_value eq $id");
            if(!empty($account_datas) AND isset($account_datas->value) AND count($account_datas->value) > 0) {
                foreach($account_datas->value as $account) {
                    $account_infos[] = array(
                        'id'=> $account->accountid,
                        'name'=> $account->name 
                    );
                }
            }
        }

        if(count($account_infos) > 1) {

            if(isset($_GET['select_account'])) return false;

            else if(!isset($_GET['selected_account']) AND !isset($_GET['select_account'])) {
                var_export();
                echo '<script>
                        sessionStorage.setItem("accounts_list", JSON.stringify(' . json_encode($account_infos) . '));
                        window.location.href= "?select_account=1";
                </script>';
                return;
            } 
            
            else if (isset($_GET['selected_account']) and preg_match('([a-zA-z0-9-]+)', $_GET['selected_account'], $matches)) {
                $account_id = $matches[0];
            } 
        } 
        
        else if(count($account_infos === 1)) $account_id = $account_infos[0]['id'];
        
        else die('<h1>اطلاعات شما یافت نشد! لطفا از مشتری معتبر استفاده کنید.</h1>');

        update_user_meta($user_id, 'account_id', $account_id);

        return $account_id;
    }

    public function reintegrate_tickets() {

        foreach($this->user_tickets as &$ticket) {
            parent::set_date_and_time($ticket);
            parent::set_ticket_status($ticket);
            parent::set_ticket_importance_status($ticket);
            parent::set_ticket_modified_status($ticket);
        }
        unset($ticket);
    }

    public function set_tickets_metadata() {

        foreach($this->user_tickets as &$ticket) {
            $this->set_tickets_statuses($ticket);
            $this->create_filters($ticket);
        }
        unset($ticket);

        foreach($this->tickets_statuses as &$ticket) $ticket['count'] = parent::translate_nums($ticket['count']);
        unset($ticket);

        foreach($this->filters['by_read'] as &$status) $status['count'] = parent::translate_nums($status['count']);
        unset($status);
    }

    public function set_tickets_statuses($ticket) {
        $this->tickets_statuses['all']['count']++;
        if($ticket['status'] === 'undefined') return false;
        $this->tickets_statuses[$ticket['status']]['count']++;
    }

    public function create_filters($ticket) {
        if($ticket['is_read']) $this->filters['by_read']['is_read']['count']++;
        else $this->filters['by_read']['is_unread']['count']++;
        $this->filters['by_read']['all']['count']++;
    }

    public function sort_tickets() {

        if(!wp_verify_nonce($_GET['my_nonce'], 'filter_sort_nonce')) {
            wp_send_json_error(array('message'=> 'invalid nonce'), 403);
        }
        $account_id = sanitize_text_field($_GET['account']);
        
        if(isset($_GET['by_read'])) $by_read_filter = sanitize_text_field($_GET['by_read']);
        if(isset($_GET['by_status'])) $by_status_filter = sanitize_text_field($_GET['by_status']);
        if(isset($_GET['by_date'])) $by_date_filter = sanitize_text_field($_GET['by_date']);
        unset($this->user_tickets);

        if($by_date_filter === 'creation_date') $this->user_tickets = get_transient("createdon_sorted_tickets_$account_id");
        else if ($by_date_filter === 'modified_date') {
            $this->user_tickets = get_transient("modifiedon_sorted_tickets_$account_id");
            if(!$this->user_tickets) { 
                $fetched_tickets = parent::fetch_datas(parent::$api_link . '/incidents?$select=incidentid,title,prioritycode,createdon,modifiedon,statuscode,_ownerid_value,new_supportid,new_isread&$filter=_customerid_value eq ' . $account_id . '&$orderby=modifiedon desc');
                $fetched_tickets = json_decode(json_encode($fetched_tickets->value), true);
                foreach($fetched_tickets as $ticket) $this->user_tickets[] = parent::structure_ticket($ticket, $my_supports);
                $this->reintegrate_tickets();
                set_transient("modifiedon_sorted_tickets_$account_id", $this->user_tickets, HOUR_IN_SECONDS);
                unset($fetched_tickets);
                gc_collect_cycles();
            }
        }

        if($by_status_filter === 'all') $filtered_tickets = $this->user_tickets;

        else {
            $filtered_tickets = array_filter($this->user_tickets, function ($ticket) use ($by_status_filter) {
                return $by_status_filter == $ticket['status'];
            });
        }
        
        if($by_read_filter !== 'all') {
            $filtered_tickets = array_filter($filtered_tickets, function ($ticket) use ($by_read_filter) {
                if($by_read_filter === 'is_read') return $ticket['is_read'];
                else if($by_read_filter === 'is_unread') return !$ticket['is_read'];
            });
        }

        $filtered_tickets = array_values($filtered_tickets);
        unset($this->user_tickets);
        gc_collect_cycles();
        
        $output = "";
        $counter = 0;
        foreach($filtered_tickets as $i=>$ticket) {
            if($counter >= $this->tickets_per_page) break;
            ob_start();
            include CTM_TEMPLATES_PATH . 'sections/ticket-item.php';
            $output .= ob_get_clean();
            $counter++;
        }

        // Free filtered tickets array
        unset($filtered_tickets);
        gc_collect_cycles();

        wp_send_json_success(array($output));
    }

    public function page_tickets() {
        
        if(!wp_verify_nonce($_GET['nonce'], 'tickets_pagination_nonce')) {
            wp_send_json_error(array('message'=> 'invalid nonce'), 403);
        }

        $sorting = sanitize_text_field($_GET['sort']);
        $account_id = sanitize_text_field($_GET['account']);

        if($sorting === 'creation_date') $this->user_tickets = get_transient("createdon_sorted_tickets_$account_id");
        else if ($sorting === 'modified_date') $this->user_tickets = get_transient("modifiedon_sorted_tickets_$account_id");
        
        if(!$this->user_tickets) { 
            $fetched_tickets = parent::fetch_datas(parent::$api_link . '/incidents?$select=incidentid,title,prioritycode,createdon,modifiedon,statuscode,_ownerid_value,new_supportid,new_isread&$filter=_customerid_value eq ' . $account_id . '&$orderby=modifiedon desc');
            $fetched_tickets = json_decode(json_encode($fetched_tickets->value), true);
            $my_supports = parent::get_supports();
            foreach($fetched_tickets as $ticket) $this->user_tickets[] = parent::structure_ticket($ticket, $my_supports);
            $this->reintegrate_tickets();
            set_transient("modifiedon_sorted_tickets_$account_id", $this->user_tickets, HOUR_IN_SECONDS);
            unset($fetched_tickets);
            gc_collect_cycles();
        }

        $requested_page = intval(sanitize_text_field($_GET['page']));
        $end_index = $this->tickets_per_page * $requested_page;
        $start_index = $end_index - $this->tickets_per_page;

        $output = '';
        for($start_index; $start_index < $end_index; $start_index++) {
            if($this->user_tickets[$start_index]) {
                $ticket = $this->user_tickets[$start_index];
                ob_start();
                include CTM_TEMPLATES_PATH . 'sections/ticket-item.php';
                $output .= ob_get_clean();
            }
        }

        wp_send_json_success(array($output));
    }

    private function create_pagination() {
        $ticket_counts = count($this->user_tickets);
        $ticket_pages = $ticket_counts / $this->tickets_per_page;
        
        if(is_float($ticket_pages)) {
            $ticket_pages = intval($ticket_pages);
            $ticket_pages++;
        }

        return $ticket_pages;
    }

}