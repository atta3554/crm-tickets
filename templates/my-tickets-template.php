<div class="ctm-container">
    <div class="ctm-container-inner">

        <div class="ctm-section-title">
            <h3 class="title">تیکت‌های پشتیبانی</h3>
        </div>

        <div class="ctm-section-container" data-account="<?= $account_id ?>">
            <div class="section-container-inner">

                <div class="ctm-section-header">
                    <div class="section-header-inner">
                        <h4 class="header-title">همه تیکت‌ها</h4>
                        <a href="<?= site_url('/my-tickets/new-ticket') ?>" class="ctm-new-ticket">ارسال تیکت جدید</a>
                    </div>
                </div>

                <div class="ctm-tickets-status">
                    <div class="tickets-status-inner">
                        <?php foreach($this->tickets_statuses as $status=>$ticket) : ?>
                        <div class="ticket-status <?= $status ?>">
                            <div class="ticket-status-inner">
                                <div class="status-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" id="Capa_1" enable-background="new 0 0 512 512" height="512" viewBox="0 0 512 512" width="512" class=""><g><g><path d="m346.5 213.5c4.143 0 7.5-3.358 7.5-7.5v-25c0-4.142-3.357-7.5-7.5-7.5s-7.5 3.358-7.5 7.5v25c0 4.142 3.357 7.5 7.5 7.5z" data-original="#000000" class="active-path" style="fill:#707070" data-old_color="#000000"/><path d="m339 267c0 4.142 3.357 7.5 7.5 7.5s7.5-3.358 7.5-7.5v-23c0-4.142-3.357-7.5-7.5-7.5s-7.5 3.358-7.5 7.5z" data-original="#000000" class="active-path" style="fill:#707070" data-old_color="#000000"/><path d="m339 330c0 4.142 3.357 7.5 7.5 7.5s7.5-3.358 7.5-7.5v-24c0-4.142-3.357-7.5-7.5-7.5s-7.5 3.358-7.5 7.5z" data-original="#000000" class="active-path" style="fill:#707070" data-old_color="#000000"/><path d="m496.86 214.184c8.772-1.59 15.14-9.238 15.14-18.186v-69.998c0-15.164-12.337-27.5-27.5-27.5h-111c-10.926 0-20.674 5.111-27 13.061-6.326-7.95-16.074-13.061-27-13.061h-217.5c-4.143 0-7.5 3.358-7.5 7.5s3.357 7.5 7.5 7.5h217.5c10.752 0 19.5 8.748 19.5 19.5v11.988c0 4.142 3.357 7.5 7.5 7.5s7.5-3.358 7.5-7.5v-11.988c0-10.752 8.748-19.5 19.5-19.5h111c6.893 0 12.5 5.607 12.5 12.5v69.998c0 1.69-1.184 3.131-2.815 3.426-27.341 4.956-47.185 28.75-47.185 56.576s19.844 51.62 47.185 56.576c1.632.295 2.815 1.736 2.815 3.426v29.498c0 4.142 3.357 7.5 7.5 7.5s7.5-3.358 7.5-7.5v-29.498c0-8.948-6.367-16.596-15.14-18.186-20.199-3.661-34.86-21.248-34.86-41.816s14.661-38.155 34.86-41.816z" data-original="#000000" class="active-path" style="fill:#707070" data-old_color="#000000"/><path d="m504.5 373c-4.143 0-7.5 3.358-7.5 7.5v5.5c0 6.893-5.607 12.5-12.5 12.5h-111c-10.752 0-19.5-8.748-19.5-19.5 0-3.088 0-9.976 0-13 0-4.142-3.357-7.5-7.5-7.5s-7.5 3.358-7.5 7.5v13c0 10.752-8.748 19.5-19.5 19.5h-292c-6.893 0-12.5-5.607-12.5-12.5v-69.998c0-1.689 1.184-3.131 2.815-3.426 27.341-4.956 47.185-28.75 47.185-56.576s-19.844-51.62-47.185-56.576c-1.632-.295-2.815-1.737-2.815-3.426v-69.998c0-6.893 5.607-12.5 12.5-12.5h39.5c4.143 0 7.5-3.358 7.5-7.5s-3.357-7.5-7.5-7.5h-39.5c-15.163 0-27.5 12.336-27.5 27.5v69.998c0 8.947 6.367 16.596 15.14 18.186 20.199 3.661 34.86 21.248 34.86 41.816s-14.661 38.155-34.86 41.816c-8.773 1.591-15.14 9.239-15.14 18.186v69.998c0 15.164 12.337 27.5 27.5 27.5h292c10.926 0 20.674-5.111 27-13.061 6.326 7.95 16.074 13.061 27 13.061h111c15.163 0 27.5-12.336 27.5-27.5v-5.5c0-4.142-3.357-7.5-7.5-7.5z" data-original="#000000" class="active-path" style="fill:#707070" data-old_color="#000000"/><path d="m108 256c0 4.142 3.357 7.5 7.5 7.5h169c4.143 0 7.5-3.358 7.5-7.5s-3.357-7.5-7.5-7.5h-169c-4.143 0-7.5 3.358-7.5 7.5z" data-original="#000000" class="active-path" style="fill:#707070" data-old_color="#000000"/><path d="m115.5 323.5h81.5c4.143 0 7.5-3.358 7.5-7.5s-3.357-7.5-7.5-7.5h-81.5c-4.143 0-7.5 3.358-7.5 7.5s3.357 7.5 7.5 7.5z" data-original="#000000" class="active-path" style="fill:#707070" data-old_color="#000000"/><path d="m248.5 203.5c4.143 0 7.5-3.358 7.5-7.5s-3.357-7.5-7.5-7.5h-97c-4.143 0-7.5 3.358-7.5 7.5s3.357 7.5 7.5 7.5z" data-original="#000000" class="active-path" style="fill:#707070" data-old_color="#000000"/><path d="m284.5 308.5h-42.5c-4.143 0-7.5 3.358-7.5 7.5s3.357 7.5 7.5 7.5h42.5c4.143 0 7.5-3.358 7.5-7.5s-3.357-7.5-7.5-7.5z" data-original="#000000" class="active-path" style="fill:#707070" data-old_color="#000000"/></g></g></svg>
                                    <span class="status-color"></span>
                                </div>
                                <div class="status-name"><?= esc_html($ticket['status']) ?></div>
                                <div class="status-count status-color"><?= esc_html($ticket['count']) ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="ctm-filters-container">
                    <div class="filters-container-inner">
                        <span class="tickets-total-count">نمایش <span>۰</span> تا <span>۱۰</span> از <?= $this->tickets_statuses['all']['count'] ?> تیکت</span>
                        <div class="tickets-filters">
                            <form id="tickets-filters-form" class="tickets-filters-inner">
                                <input type="hidden" name='action' value="sort_tickets">
                                <input type="hidden" name="my_nonce" value="<?= wp_create_nonce('filter_sort_nonce'); ?>">
                                <?php foreach($this->filters as $type=>$filters) : ?>
                                <select name="<?= $type ?>" class="<?= $type ?>-filter">
                                    <?php foreach($filters as $status=>$val) : 
                                    $option = esc_html($val['status']);
                                    if($type != 'by_date') $option .= ' (' . esc_html($val['count']) . ')';
                                    ?>
                                        <option value="<?= $status ?>"><?= $option ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php endforeach; ?>
                                <input type="submit" class="submit-filters" id="submit-filters" value="صافی">
                            </form>
                        </div>
                    </div>
                </div>

                <div class="ctm-tickets-list">
                    <div class="tickets-list-inner">
                    <?php if(isset($this->user_tickets) AND !empty($this->user_tickets)) {
                        foreach($this->user_tickets as $i=>$ticket) {
                            if($i > 9) break;
                            include CTM_TEMPLATES_PATH . 'sections/ticket-item.php';
                        }
                    } ?>
                    </div>
                </div>

                <div class="ctm-pagination" id="ctm-pagination">
                    <span class="page-num" style="display: none;">1</span>
                    <div class="prev-page"><< قبلی</div>

                    <?php if($ticket_pages > 10) : ?>

                        <div class="first-pages">
                        <?php for($i = 1; $i <= 3; $i++) : ?>
                            <div data-id="<?= $i ?>" class="ctm-page-<?= $i ?> <?php if($i==1) echo 'active' ?>"><?= parent::translate_nums($i) ?></div>
                        <?php endfor; ?>
                        </div>

                        <div class="pagination-dots">...</div>

                        <div class="last-pages">
                        <?php for($i = $ticket_pages - 2; $i <= $ticket_pages; $i++) : ?>
                            <div data-id="<?= $i ?>" class="ctm-page-<?= $i ?>"><?= parent::translate_nums($i) ?></div>
                        <?php endfor; ?>
                        </div>
                        
                    <?php else : ?>
                        <?php for($i=1; $i <= $ticket_pages; $i++) : ?>
                            <div data-id="<?= $i ?>" class="ctm-page-<?= $i ?> <?php if($i == 1) echo 'active'; ?>"><?= parent::translate_nums($i) ?></div>
                        <?php endfor; ?>
                    <?php endif; ?>
                    <div class="next-page">بعدی >></div>
                </div>
            </div>
        </div>
    </div>
</div>