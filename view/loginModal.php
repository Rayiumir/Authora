
<?php
$digits = 5;
?>
<div id="open-modal" class="modal-window">
  <div class="authora-container">
    <a href="#" title="Close" class="modal-close">
        <svg width="25" height="25" viewbox="0 0 40 40"><path d="M 10,10 L 30,30 M 30,10 L 10,30" stroke="black" stroke-width="4" /></svg>
    </a>
    <div class="authora-modal">
        <form class="form" id="authora-login">
            <h2>ورود/ثبت نام</h2>
            <p>
                برای راحتی شما ورود و ثبت نام را با شماره تلفن شما انجام دادیم
            </p>
            <div class="authora-field">
                <label for="authora-phone">شماره همراه خود را وارد کنید</label>
                <div class="authora-cols">
                    <input type="text" inputmode="tel" placeholder="0 9 - - - - - - - - -" name="mobile" required>
                    <span>+98</span>
                </div>
            </div>
            <p class="authora-message">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                    <path id="Path_3" data-name="Path 3" d="M12,22A10,10,0,1,0,2,12,10,10,0,0,0,12,22Zm-1.5-5.009a1.5,1.5,0,0,1,3,0,1.5,1.5,0,1,1-3,0ZM11.172,6a.5.5,0,0,0-.5.522l.306,7a.5.5,0,0,0,.5.478h1.043a.5.5,0,0,0,.5-.478l.305-7a.5.5,0,0,0-.5-.522H11.172Z" transform="translate(-2 -2)" fill="#ff6363" fill-rule="evenodd"/>
                </svg>
                <span></span>
            </p>
            <button class="authora-btn-secondary">
                <svg width="36" height="36" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><style>.spinner_d9Sa{transform-origin:center}.spinner_qQQY{animation:spinner_ZpfF 9s linear infinite}.spinner_pote{animation:spinner_ZpfF .75s linear infinite}@keyframes spinner_ZpfF{100%{transform:rotate(360deg)}}</style><path d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,20a9,9,0,1,1,9-9A9,9,0,0,1,12,21Z"/><rect class="spinner_d9Sa spinner_qQQY" x="11" y="6" rx="1" width="2" height="7"/><rect class="spinner_d9Sa spinner_pote" x="11" y="11" rx="1" width="2" height="9"/></svg>
                ارسال کد یکبار مصرف
            </button>
        </form>

        <form class="form" id="authora-verify">
            <h2>تأیید شماره همراه</h2>
            <p class="authora-login-result">
                کد 5 رقمی ارسال شده به شماره  را وارد کنید
            </p>
            <?php foreach( range( 1, $digits ) as $index ):?>
                <input type="text" maxlength="1">
            <?php endforeach;?>
            <div class="authora-no-receive">
                <p>کد را دریافت نکردید؟</p>
                <div>
                    <a href="#" class="authora-resend">ارسال مجدد</a>
                    <span class="authora-countdown">01:24</span>
                </div>
            </div>
            <p class="authora-message">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                    <path id="Path_3" data-name="Path 3" d="M12,22A10,10,0,1,0,2,12,10,10,0,0,0,12,22Zm-1.5-5.009a1.5,1.5,0,0,1,3,0,1.5,1.5,0,1,1-3,0ZM11.172,6a.5.5,0,0,0-.5.522l.306,7a.5.5,0,0,0,.5.478h1.043a.5.5,0,0,0,.5-.478l.305-7a.5.5,0,0,0-.5-.522H11.172Z" transform="translate(-2 -2)" fill="#ff6363" fill-rule="evenodd"/>
                </svg>
                <span></span>
            </p>
            <div class="authora-success" style="display: none;">ورود انجام شد</div>
            <div class="authora-buttons">
                <button class="authora-btn authora-btn-primary">
                    <svg width="36" height="36" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><style>.spinner_d9Sa{transform-origin:center}.spinner_qQQY{animation:spinner_ZpfF 9s linear infinite}.spinner_pote{animation:spinner_ZpfF .75s linear infinite}@keyframes spinner_ZpfF{100%{transform:rotate(360deg)}}</style><path d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,20a9,9,0,1,1,9-9A9,9,0,0,1,12,21Z"/><rect class="spinner_d9Sa spinner_qQQY" x="11" y="6" rx="1" width="2" height="7"/><rect class="spinner_d9Sa spinner_pote" x="11" y="11" rx="1" width="2" height="9"/></svg>
                    تأیید کد
                </button>
                <button type="button" class="authora-btn authora-btn-secondary" id="authora-edit-number">
                    اصلاح شماره
                </button>
            </div>
        </form>
    </div>
  </div>
</div>