<form id="new-ticket" class="new-ticket">
    <?php if(CTM_PAGE AND CTM_PAGE === 'NEW_TICKET') : ?> <div class="back-to-archive"><a href="<?= site_url("/my-tickets/?selected_account=$account_id") ?>">بازگشت به تیکت های من</a></div><?php endif; ?>
    <input type="hidden" name="action" value="new_message" readonly>
    <?php if(CTM_PAGE AND CTM_PAGE === 'SINGLE') : ?><input id="ticket-id" type="hidden" name="ticket-id" value="<?= esc_attr($this->current_ticket['id']) ?>" readonly> <?php endif; ?>
    <input type="hidden" name="submit_nonce" id="submit_nonce" value="<?= wp_create_nonce('new_message_nonce') ?>" readonly>
    <input type="hidden" name="account_id" id="account_id" value="<?= $account_id ?>" readonly>
    
    <?php if(CTM_PAGE AND CTM_PAGE === 'NEW_TICKET') : ?>
        <div class="new-ticket-section">
            <div class="reply-title">
                <label for="ticket-reply-title">عنوان تیکت را وارد نمایید:</label>
                <input name="ticket-reply-title" id="ticket-reply-title" required>
            </div>
            <div class="ctm-ticket-importance" data-count="<?= $high_priority_tickets_count ?>">
                <span class="priority-count">تعداد تیکت هایه ثبت شده با اولویت بالا توسط شما در طول یکماه گذشته: <?= $high_priority_tickets_count ?> مورد</span>
                <label for="ticket-importance">اولویت تیکت خود را ثبت کنید (<span>هر کاربر در هر ماه مجاز به ثبت حداکثر ۳ تیکت با اولویت بالا میباشد</span>) </label>
                <div class="ticket-importance">
                    <select name="ticket-importance" id="ticket-importance" >
                        <option value="0">انتخاب کنید</option>
                        <option value="1" <?php if($high_priority_tickets_count >= 3) echo 'disabled'; ?>>اولویت بالا</option>
                        <option value="2">اولویت معمولی</option>
                        <option value="3">اولویت پایین</option>
                    </select>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="reply-content">
        <label for="ticket-reply-content">محتوای تیکت را وارد نمایید:</label>
        <textarea name="ticket-reply-content" id="ticket-reply-content" rows="7" required></textarea>
    </div>

    <div class="reply-files">
        <div class="reply-files-inner">
            
            <div class="user-reply-voices">
                
                <div class="voice-recorder">
                    <div class="voice-recorder-inner">
                        <button id="start-recording" class="start-recording">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 368 368" style="enable-background:new 0 0 368 368;" xml:space="preserve"><g><path style="fill:#fff;" d="M112,184c0,41.288,32.304,74.88,72,74.88s72-33.592,72-74.88V74.88C256,33.592,223.696,0,184,0   s-72,33.592-72,74.88V184z M184,242.88c-28.288,0-51.688-22.192-55.424-50.88h110.84C235.688,220.688,212.288,242.88,184,242.88z    M184,16c30.88,0,56,26.416,56,58.88V176H128V74.88C128,42.416,153.12,16,184,16z"/><path style="fill:#fff;" d="M136,352c-4.416,0-8,3.584-8,8s3.584,8,8,8h96c4.416,0,8-3.584,8-8s-3.584-8-8-8h-40v-48.408   c62.44-4.144,112-56.128,112-119.592v-32c0-4.416-3.584-8-8-8s-8,3.584-8,8v32c0,57.344-46.656,104-104,104S80,241.344,80,184v-32   c0-4.416-3.584-8-8-8s-8,3.584-8,8v32c0,63.464,49.56,115.448,112,119.592V352H136z"/></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>
                        </button>
                        <div class="recorder-timer">
                            <div class="hour">00</div>
                            <div class="colon">:</div>
                            <div class="minute">00</div>
                            <div class="colon">:</div>
                            <div class="second">00</div>
                        </div>
                        <button disabled class="stop-recording" id="stop-recording">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 493.56 493.56" style="enable-background:new 0 0 493.56 493.56;" xml:space="preserve" width="512px" height="512px" class=""><g><g><g><path d="M438.254,0H58.974C27.502,0,0.006,25.992,0.006,57.472v379.256c0,31.48,27.496,56.832,58.968,56.832h379.28    c31.468,0,55.3-25.352,55.3-56.832V57.472C493.554,25.992,469.722,0,438.254,0z" data-original="#000000" class="active-path" data-old_color="#000000" fill="#EB3B5A"/></g></g></g></svg>
                        </button>
                        <input type="file" name="audio-input" id="audio-input" accept="audio/*" hidden>
                    </div>
                </div>

                <div class="user-recorded-voice"></div>
            </div>
            
            <div class="user-reply-attachments" id="user-reply-attachments">
                <div class="reply-attachments-inner">
                    <div class="uploaded-file-container"></div>
                    <p class="upload-text">فایلها اینجا رها کنید، بچسبانید یا <span id="browse-btn">انتخاب کنید</span></p>
                    <input type="file" name="file-input" id="file-input" accept=".doc,.docx,image/*" hidden>
                    <div class="highlight">
                        <p>فایل‌ها را بکشید و اینجا رها کنید</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="ctm-columns-infos">
        <div class="columns-infos-inner">
            <div></div>
            <div class="attachment-descriptions">
                <p>حداکثر اندازه: 5 مگابایت  |  فرمت‌های مجاز: png, jpeg, jpg, zip, rar</p>
            </div>
        </div>
    </div>

    <div class="ctm-send-ticket">
        <div class="send-ticket-inner">
            <div class="status-close-ticket">
                <?php if(CTM_PAGE AND CTM_PAGE === 'SINGLE') : ?><label for="close-ticket"><input type="checkbox" id="close-ticket" name="status" value="closed"> بستن تیکت</label> <?php endif; ?>
            </div>
            <div class="send-ticket">
                <button class="send-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 511.92 511.92" style="enable-background:new 0 0 511.92 511.92;" xml:space="preserve" width="512" height="512"><g><g><g><path d="M505.77,46.551c-4.773-3.978-11.344-5.013-17.109-2.697L56.482,218.293c-4.371,1.767-6.482,6.744-4.715,11.115    s6.744,6.482,11.115,4.715l412.86-166.588l-204.8,241.545c-3.047,3.596-2.602,8.981,0.994,12.028    c3.596,3.047,8.981,2.602,12.028-0.994L491.861,74.899l-27.785,139.469l-27.989,187.025c-0.384,2.617-1.96,4.909-4.267,6.204    c-2.212,1.235-4.877,1.358-7.194,0.333l-116.369-45.756l-55.842-23.381c-4.345-1.823-9.346,0.22-11.17,4.565    c-0.186,0.585-0.307,1.189-0.358,1.801h-0.205l-27.725,107.059l-39.782-138.965l207.36-154.453    c2.65-1.741,4.117-4.807,3.808-7.963c-0.309-3.156-2.342-5.88-5.279-7.074c-2.937-1.194-6.295-0.661-8.718,1.384L161.92,300.324    L11.682,239.507c-4.36-1.729-9.298,0.384-11.057,4.732c-1.759,4.348,0.321,9.3,4.657,11.089l151.04,61.116l40.277,140.612    c2.097,7.263,8.756,12.254,16.316,12.228h0.196c4.121-0.074,8.07-1.663,11.093-4.463c0.298-0.179,0.583-0.379,0.853-0.597    l82.483-84.343l110.592,43.853c7.107,3.019,15.214,2.565,21.939-1.229c6.93-3.844,11.682-10.689,12.86-18.526l27.913-186.675    l30.72-154.121C512.842,56.995,510.616,50.605,505.77,46.551z M236.877,427.786l18.014-69.436l28.211,11.844l7.27,2.884    L236.877,427.786z" data-original="#000000" class="active-path" style="fill:#FFFFFF" data-old_color="#000000"/></g></g></g></svg>
                    ارسال تیکت
                </button>
            </div>
        </div>
    </div>
</form>