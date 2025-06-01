<div class="ctm-container">
    <div class="ctm-container-inner">
        
        <div class="ctm-section-title">
            <h3 class="title">تیکت‌های پشتیبانی</h3>
        </div>

        <div class="ctm-section-container">
            <div class="section-container-inner">
                
                <div class="ctm-section-header">
                    <div class="section-header-inner">
                        <h4 class="header-title">مشاهده تیکت</h4>
                        <a href="<?= esc_url(site_url('my-tickets') . "/?selected_account=$account_id") ?>" class="ctm-all-tickets">همه تیکت ها</a>
                    </div>
                </div>

                <div class="ctm-notice">
                    <div class="ctm-notice-inner">
                        <div class="icon">
                            <svg height="24" version="1.1" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15.984 17.016v-6c0-2.484-1.5-4.5-3.984-4.5s-3.984 2.016-3.984 4.5v6h7.969zM18 15.984l2.016 2.016v0.984h-16.031v-0.984l2.016-2.016v-4.969c0-3.094 1.641-5.625 4.5-6.328v-0.703c0-0.844 0.656-1.5 1.5-1.5s1.5 0.656 1.5 1.5v0.703c2.859 0.703 4.5 3.281 4.5 6.328v4.969zM12 21.984c-1.078 0-2.016-0.891-2.016-1.969h4.031c0 1.078-0.938 1.969-2.016 1.969z" fill="#fff"></path>
                            </svg>
                        </div>
                        <div class="notice-message">پشتیبانی از کاربران قبل و بعد از خرید آنها، از بدیهی ترین امکاناتی است که باید در اختیار کاربران و مشتریان فروشگاه قرار بگیرد.</div>
                    </div>
                </div>

                <div class="ctm-title-container">
                    <div class="ctm-title-inner">
                        <div class="back-icon">
                            <a href="<?= esc_url(site_url('/my-tickets')) ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 407.436 407.436" style="enable-background:new 0 0 407.436 407.436;" xml:space="preserve" width="512" height="512"><g><polygon points="315.869,21.178 294.621,0 91.566,203.718 294.621,407.436 315.869,386.258 133.924,203.718 " data-original="#000000" class="active-path" data-old_color="#000000" style="fill:#8D99A9"/></g></svg>
                            </a>
                        </div>
                        <div class="title-info">
                            <h4 class="title"><?= esc_html($this->current_ticket['title']) ?></h4>
                            <div class="ticket-id">شناسه تیکت: <span><?= esc_html($this->current_ticket['ticket_id']) ?></span></div>
                        </div>
                    </div>
                </div>

                <div class="ctm-ticket-container" id="ctm-ticket-container" data-read="<?= $this->current_ticket['is_read'] ?>">
                    <div class="ticket-container-inner">
                        
                        <div class="ctm-ticket-content">
                            <div class="ticket-content-inner">

                                <div class="ticket-files-info">
                                    <div class="ticket-files">

                                        <div class="voice-files">
                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 435.2 435.2" style="enable-background:new 0 0 435.2 435.2;" xml:space="preserve" width="512" height="512"><g><g><g><path d="M356.864,224.768c0-8.704-6.656-15.36-15.36-15.36s-15.36,6.656-15.36,15.36c0,59.904-48.64,108.544-108.544,108.544    c-59.904,0-108.544-48.64-108.544-108.544c0-8.704-6.656-15.36-15.36-15.36c-8.704,0-15.36,6.656-15.36,15.36    c0,71.168,53.248,131.072,123.904,138.752v40.96h-55.808c-8.704,0-15.36,6.656-15.36,15.36s6.656,15.36,15.36,15.36h142.336    c8.704,0,15.36-6.656,15.36-15.36s-6.656-15.36-15.36-15.36H232.96v-40.96C303.616,355.84,356.864,295.936,356.864,224.768z" data-original="#000000" class="active-path" style="fill:#8D99A9" data-old_color="#000000"/></g></g><g><g><path d="M217.6,0c-47.104,0-85.504,38.4-85.504,85.504v138.752c0,47.616,38.4,85.504,85.504,86.016    c47.104,0,85.504-38.4,85.504-85.504V85.504C303.104,38.4,264.704,0,217.6,0z" data-original="#000000" class="active-path" style="fill:#8D99A9" data-old_color="#000000"/></g></g></g></svg>
                                            <span class="voices-count">۰</span>
                                        </div>
                                        
                                        <div class="attachment-files">
                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 330.001 330.001" style="enable-background:new 0 0 330.001 330.001;" xml:space="preserve" width="512" height="512"><g><path id="XMLID_197_" d="M194.999,0C194.999,0,194.999,0,194.999,0c-20.033,0-38.866,7.801-53.031,21.966  c-14.166,14.166-21.967,33-21.968,53.033v135.004c0,24.813,20.186,44.999,45.001,45c24.813-0.001,44.999-20.189,44.999-45.002  v-77.902c0-8.284-6.716-15-15-15c-8.284,0-15,6.716-15,15v77.902c0,8.272-6.73,15.001-14.999,15.002  c-8.271,0-15.001-6.729-15.001-15V74.999c0-12.02,4.682-23.321,13.181-31.82c8.5-8.5,19.799-13.18,31.818-13.18  c24.814,0,45.001,20.186,45.002,44.998v150.002c-0.002,41.355-33.646,75.001-75,75.001c-20.033,0-38.868-7.8-53.033-21.966  c-14.166-14.165-21.967-33-21.967-53.034L89.999,74.999c0-8.285-6.716-15-15-15s-15,6.716-15,15l0.002,150.001  C60,253.047,70.922,279.415,90.754,299.248c19.832,19.832,46.2,30.754,74.247,30.753c57.895,0,104.998-47.103,105-105V74.998  C270,33.644,236.354,0,194.999,0z" data-original="#000000" class="active-path" data-old_color="#000000" style="fill:#8D99A9"/></g> </svg>
                                            <span class="attachments-count">۴</span>
                                        </div>
                                        
                                    </div>
                                    <div class="toggle-sidebar">
                                        <svg xmlns="http://www.w3.org/2000/svg" id="Layer" enable-background="new 0 0 64 64" height="512" viewBox="0 0 64 64" width="512"><g><path d="m50 8h-36c-3.309 0-6 2.691-6 6v36c0 3.309 2.691 6 6 6h36c3.309 0 6-2.691 6-6v-36c0-3.309-2.691-6-6-6zm-38 42v-36c0-1.103.897-2 2-2h8v40h-8c-1.103 0-2-.897-2-2zm40 0c0 1.103-.897 2-2 2h-24v-40h24c1.103 0 2 .897 2 2z" data-original="#000000" class="active-path" data-old_color="#000000" style="fill:#8D99A9"/></g> </svg>
                                    </div>
                                </div>

                                <?php if(!in_array($this->current_ticket['status_code'], ['5', '6', '1000', '2000'])) : ?>

                                    <div class="ticket-reply-toggle">
                                        <div class="reply-toggle-btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve" width="512" height="512"><g><g><g><path d="M492,236H276V20c0-11.046-8.954-20-20-20c-11.046,0-20,8.954-20,20v216H20c-11.046,0-20,8.954-20,20s8.954,20,20,20h216    v216c0,11.046,8.954,20,20,20s20-8.954,20-20V276h216c11.046,0,20-8.954,20-20C512,244.954,503.046,236,492,236z" data-original="#000000" class="active-path" style="fill:#8D99A9" data-old_color="#000000"/></g></g></g></svg>
                                            <span>ارسال پاسخ</span>
                                        </div>
                                    </div>

                                    <?php include_once CTM_TEMPLATES_PATH . 'sections/new-ticket.php' ?>

                                <?php endif; ?>

                                <div class="ticket-messages-container">
                                    <div class="ticket-messages-inner">
                                        
                                        <?php foreach($this->current_ticket['dialog'] as $dialog) : ?>
                                        <div class="dialog-box <?= esc_attr($dialog['owner']) ?>" data-id="<?= esc_attr($dialog['annotation_id']) ?>">
                                            <div class="dialog-box-inner">

                                                <div class="message-box">
                                                    <div class="message-box-inner">
                                                        <p><?= esc_html($dialog['content']) ?></p>
                                                        <?php if(isset($dialog['filename']) AND !empty($dialog['filename'])) : ?>
                                                        <div class="ctm-file-container">
                                                            <button>
                                                                <div class="file-icon"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><polygon style="fill:#FFE182;" points="360.129,172.138 256,472.276 512,172.138 "/><g><polygon style="fill:#FFCD73;" points="105.931,39.724 0,172.138 151.871,172.138  "/><polygon style="fill:#FFCD73;" points="360.129,172.138 512,172.138 406.069,39.724  "/><polygon style="fill:#FFCD73;" points="360.129,172.138 256,39.724 151.871,172.138  "/></g><polygon style="fill:#FFAA64;" points="256,39.724 105.931,39.724 151.871,172.138 "/><polygon style="fill:#FFE182;" points="406.069,39.724 256,39.724 360.129,172.138 "/><polygon style="fill:#FFAA64;" points="151.871,172.138 256,472.276 360.129,172.138 "/><polygon style="fill:#FF8C5A;" points="0,172.138 256,472.276 151.871,172.138 "/></svg></div>
                                                                <div class="file-datas">
                                                                    <span class="file-name"><?= esc_html($dialog['filename']) ?></span>
                                                                    <span class="download-icon"><svg xmlns="http://www.w3.org/2000/svg" height="44" viewBox="0 0 512 512" width="18" class=""><g><path d="m409.785156 278.5-153.785156 153.785156-153.785156-153.785156 28.285156-28.285156 105.5 105.5v-355.714844h40v355.714844l105.5-105.5zm102.214844 193.5h-512v40h512zm0 0" data-original="#000000" class="active-path" style="fill:#8D99A9" data-old_color="#000000"/></g></svg></span>
                                                                </div>
                                                            </button>
                                                        </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <div class="message-metadata">
                                                    <div class="metadata-inner">
                                                        <div class="<?= $dialog['class_name'] ?>" <?php if($dialog['class_name'] === 'user-image') echo "style='background-image: url(" . esc_url(get_avatar_url(get_current_user_id())) .");'"; ?> ></div>
                                                        <span class="owner-name"><?= $dialog['owner_name'] ?> - <?= parent::translate_nums($dialog['jalali_created']) ?></span>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <?php unset($src); endforeach; ?>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <aside class="ctm-ticket-metadata">
                            <div class="ticket-metadata-inner">

                                <div class="ticket-rating">
                                    <p>با درج امتیاز ما را در ارائه خدمات هرچه بهتر یاری نمایید.</p>
                                    <form id="ctm-ticket-rating">
                                        <input type="hidden" name="ticket-id" value="<?= esc_html($this->current_ticket['id']) ?>">
                                        <input type="hidden" name="rate_nonce" value="<?= wp_create_nonce('ticket_rating_nonce') ?>">
                                        <input type="hidden" name="action" value="rate_ticket">
                                        <fieldset class="ctm-rating">
                                            <?php for($i = 5; $i >= 1; $i--) : ?>
                                            <input type="radio" id="ctm-star-<?= $i ?>" name="rating" value="<?= $i ?>" <?php if($i == $this->current_ticket['ticket_rate']) echo "checked"; ?> >
                                            <label for="ctm-star-<?= $i ?>" title="<?= parent::translate_nums($i) ?> ستاره"></label>
                                            <?php endfor; ?>
                                        </fieldset>
                                        <button type="submit">ثبت امتیاز</button>
                                    </form>
                                </div>

                                <div class="ticket-metadata">

                                    <div class="ctm-ticket-status">
                                        <div class="ticket-status-inner <?= esc_attr($this->current_ticket['status']) ?>">
                                            <span class="status-color"></span>
                                            <span class="ticket-status"><?= esc_html(ArchiveTicketsHandler::instance()->tickets_statuses[$this->current_ticket['status']]['status']) ?></span> <!-- esc_html($this->tickets_statuses[$this->current_ticket['status']]['status']); -->
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="ctm-ticket-date">
                                        <div class="ticket-date-inner">
                                            <div class="ticket-date-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve" width="512" height="512"><g><g><g><path d="M256,0C114.841,0,0,114.841,0,256s114.841,256,256,256s256-114.841,256-256S397.159,0,256,0z M256,468.732    c-117.301,0-212.732-95.431-212.732-212.732S138.699,43.268,256,43.268S468.732,138.699,468.732,256S373.301,468.732,256,468.732z    " data-original="#000000" class="active-path" data-old_color="#000000" style="fill:#8D99A9"/></g></g><g><g><path d="M372.101,248.068H271.144V97.176c0-11.948-9.686-21.634-21.634-21.634c-11.948,0-21.634,9.686-21.634,21.634v172.525    c0,11.948,9.686,21.634,21.634,21.634c0.244,0,0.48-0.029,0.721-0.036c0.241,0.009,0.477,0.036,0.721,0.036h121.149    c11.948,0,21.634-9.686,21.634-21.634S384.049,248.068,372.101,248.068z" data-original="#000000" class="active-path" data-old_color="#000000" style="fill:#8D99A9"/></g></g></g></svg>
                                            </div>
                                            <div class="ticket-date-content">
                                                <span class="ticket-answered-date" dir="ltr"><?= esc_html(parent::translate_nums(esc_html($this->current_ticket['jalali_created']))) ?></span>
                                                <div class="ticket-created-date" title="<?= esc_attr(parent::translate_nums(esc_html($this->current_ticket['jalali_modified']))) ?>">آخرین اقدام: <span><?= $this->current_ticket['modified_status'] ?></span></div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="ctm-ticket-importance">
                                        <div class="ticket-importance-inner">
                                            <div>اولویت:‌ <span><?= esc_html($this->current_ticket['importance_name']) ?></span></div>
                                            <div class="ctm-rating"><?php for($i=0; $i<intval($this->current_ticket['importance']); $i++) :?><span></span> <?php endfor; ?></div>
                                        </div>
                                    </div>

                                </div>

                                <div class="ticket-support">
                                    <div class="support-img support-<?= $this->current_ticket['support_id'] ?>"></div>
                                    <div class="support-name"><?= $this->current_ticket['support'] ?></div>
                                </div>
                            </div>
                        </aside>

                    </div>
                </div>

            </div>
        </div>

    </div>
</div>