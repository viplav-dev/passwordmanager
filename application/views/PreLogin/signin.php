<body>
    <div class="signInContainer">

        <div class="signInForm">
            <div class="signInTitle">Sign In</div>
            <?php

            echo form_open(base_url("account/login"));

            ?>



            <label for="email">Email:</label>
            <input class="signInInput" placeholder="tony@starkindustries.com" name="email" type="email" autofocus required>

            <label for="password">Password:</label>
            <input id="password" class="signInInput" placeholder="●●●●●●●●●●" name="password" type="password" value="" required>

            <!-- Show Password -->
            <div style="display:flex;flex-direction:row;justify-content:end">
            <span class="showPasswordBtn" onclick="show_password()"> Show Password</span>
            
            </div>
            <!-- <div class="checkbox">
                <label>
                    <input name="remember" type="checkbox" value="Remember Me">Remember Me
                </label>
            </div> -->
            <div class="signInBtnContainer">
            <a class="showPasswordBtn" href="<?php echo base_url("account/forgotPassword") ?>" > Forgot Password?</a>
                <!-- Change this to a button or input when using this as a form -->
                <input type="submit" class="signInBtn" class="btn btn-lg btn-success btn-block" value="Login" />
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