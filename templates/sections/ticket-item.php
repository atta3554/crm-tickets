<div class="ctm-ticket-item ticket-item-<?= esc_attr($ticket['id']) . ' ' . esc_attr($ticket['status']) ?>" id="ctm-ticket-<?= esc_attr($ticket['id']) ?>">
    <div class="ticket-item-inner">
        <div class="ctm-item-title">
            <div class="item-title-inner">
                <a href="<?= esc_url(site_url() . '/my-tickets/ticket-' . $ticket['id']) ?>" class="ctm-ticket-title"><?= esc_html($ticket['title']) ?></a>
                <div class="ticket-id">
                    <svg xmlns="http://www.w3.org/2000/svg" id="Capa_1" enable-background="new 0 0 515.555 515.555" height="512" viewBox="0 0 515.555 515.555" width="512"><g><path d="m303.347 18.875c25.167 25.167 25.167 65.971 0 91.138s-65.971 25.167-91.138 0-25.167-65.971 0-91.138c25.166-25.167 65.97-25.167 91.138 0" data-original="#000000" class="active-path" style="fill:#8D99A9" data-old_color="#000000"/><path d="m303.347 212.209c25.167 25.167 25.167 65.971 0 91.138s-65.971 25.167-91.138 0-25.167-65.971 0-91.138c25.166-25.167 65.97-25.167 91.138 0" data-original="#000000" class="active-path" style="fill:#8D99A9" data-old_color="#000000"/><path d="m303.347 405.541c25.167 25.167 25.167 65.971 0 91.138s-65.971 25.167-91.138 0-25.167-65.971 0-91.138c25.166-25.167 65.97-25.167 91.138 0" data-original="#000000" class="active-path" style="fill:#8D99A9" data-old_color="#000000"/></g></svg>
                    شناسه تیکت:‌<span><?= esc_html($ticket['ticket_id']) ?></span>
                </div>
            </div>
        </div>

        <div class="ctm-item-support">
            <div class="item-support-inner">
                <div class="support-img support-<?=(intval(esc_attr($ticket['support_id'])))?>"></div>
                <span class="support-name"><?=$ticket['support']?></span>
                <div class="has-notification">
                    <?php if($ticket['is_read'] === 'false') echo '<span></span>'; ?>
                    <svg xmlns="http://www.w3.org/2000/svg" id="Outlined" viewBox="0 0 32 32"><title/><g id="Fill"><path d="M26,3H6A3,3,0,0,0,3,6V30.41l5.12-5.12A1.05,1.05,0,0,1,8.83,25H26a3,3,0,0,0,3-3V6A3,3,0,0,0,26,3Zm1,19a1,1,0,0,1-1,1H8.83a3,3,0,0,0-2.12.88L5,25.59V6A1,1,0,0,1,6,5H26a1,1,0,0,1,1,1Z"/><rect height="2" width="12" x="10" y="11"/><rect height="2" width="7" x="10" y="15"/></g></svg>
                </div>
            </div>
        </div>

        <div class="ctm-ticket-importance">
            <div class="ticket-importance-inner">
                <div>اهمیت:‌ <span><?=$ticket['importance_name']?></span></div>
                <div class="ctm-rating"><?php for($i = 0; $i < intval($ticket['importance']); $i++) :?><span></span><?php endfor; ?></div>
            </div>
        </div>

        <div class="ctm-ticket-status">
            <div class="ticket-status-inner <?= esc_attr($ticket['status']) ?>">
                <span class="status-color"></span>
                <span class="ticket-status"><?= esc_html($this->tickets_statuses[$ticket['status']]['status']) ?></span>
            </div>
        </div>

        <div class="ctm-ticket-date">
            <div class="ticket-date-inner">
                <div class="ticket-answered-date" dir="ltr">تاریخ ایجاد: <span><?= esc_html(parent::translate_nums($ticket['jalali_created'])) ?></span></div>
                <div class="ticket-created-date" title="<?= esc_attr(parent::translate_nums($ticket['jalali_modified'])) ?>">آخرین اقدام:  <span><?= esc_html($ticket['modified_status']) ?></span></div>
            </div>
        </div>

        <div class="ctm-view-ticket">
            <div class="view-ticket-inner">
                <a href="<?= esc_url(site_url() . '/my-tickets/ticket-' . $ticket['id']) ?>" class="btn view-ticket-">مشاهده تیکت</a>
            </div>
        </div>

    </div>
</div>