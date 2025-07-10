<div class="phem-container">
    <div class="phem-card">
        <img class="phe-login-img" width="250px" src="https://storage.googleapis.com/prod-phoneemail-prof-images/phem-widgets/phe-signin-box.svg" alt="phone email login demo">
        <h1 style="margin:10px; 0">Sign In</h1>
        <p style="color:#a6a6a6;">Welcome to Sign In with Phone</p>
        <button style="display: flex; align-items: center; justify-content:center; padding: 14px 20px; background-color: #02BD7E; font-weight: bold; color: #ffffff; border: none; border-radius: 3px; font-size: inherit;cursor:pointer; max-width:320px; width:100%" id="btn_ph_login" name="btn_ph_login" type="button" onclick="window.open('{{ $AUTH_URL }}', 'peLoginWindow', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0, width=500, height=560, top=' + (screen.height - 600) / 2 + ', left=' + (screen.width - 500) / 2);">
            <img src="https://storage.googleapis.com/prod-phoneemail-prof-images/phem-widgets/phem-phone.svg" alt="phone email" style="margin-right:10px;">
            Sign In with Phone
        </button>
    </div>
</div>