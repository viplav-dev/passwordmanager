<body>
    <div class="signInContainer">

        <div class="signInForm">
            <div class="signInTitle">Forgot Password</div>
            <p style="max-width:325px;">Please enter your registered email address to receive password reset link.</p>
            <?php

            echo form_open(base_url("account/sendresetlink"));

            ?>



            <label for="email">Email:</label>
            <input class="signInInput" placeholder="tony@starkindustries.com" name="email" type="email" autofocus required>

            
            <div class="signInBtnContainer" style="justify-content:end!important;">
           
                <input type="submit" style="width:fit-content!important ;" class="signInBtn" class="btn btn-lg btn-success btn-block" value="Send Link" />
            </div>
            </form>
        </div>

    </div>
</body>
<script>
    function show_password() {
        var x = document.getElementById("password");
        var y = document.getElementsByClassName("showPasswordBtn");
        if (x.type === "password") {
            x.type = "text";
            y[0].innerHTML = "Hide Password";
        } else {
            x.type = "password";
            y[0].innerHTML = "Show Password";
        }
    }
</script>