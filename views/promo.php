<style>
	.zrdn-discount {
		border:1px solid black;
		background-color:white;
		padding:10px;
		margin:20px;
	}
	.zrdn-discount ul {
		list-style-type: circle;
		margin-left: 30px;}
</style>

<script>
    jQuery(document).ready(function ($) {

        $(document).on("click", "#zrdn-dismiss-discount", function (event) {
            zrdn_dismiss_discount_notice();
            $(this).parent('.zrdn-discount').hide();
        });

        function zrdn_dismiss_discount_notice() {
            var data = {
                'action': 'zrdn_dismiss_discount_notice',
            };
            $.post(ajaxurl, data, function (response) {
            });
        }

        var timer2 = "{hours_left}:{minutes_left}:{seconds_left}";
        var interval = setInterval(function () {
            var timer = timer2.split(':');
            //by parsing integer, I avoid all extra string processing
            var hours = parseInt(timer[0], 10);
            var minutes = parseInt(timer[1], 10);
            var seconds = parseInt(timer[2], 10);
            --seconds;

            minutes = (seconds < 0) ? --minutes : minutes;
            if (minutes < 0) clearInterval(interval);

            minutes = (minutes < 10 && minutes > 0) ? '0' + minutes : minutes;
            minutes = (minutes < 0 ) ? 60 + minutes : minutes;

            hours = (minutes < 0) ? --hours : hours;
            if (hours < 0) clearInterval(interval);
            seconds = (seconds < 0) ? 59 : seconds;
            seconds = (seconds < 10) ? '0' + seconds : seconds;

            $('.countdown').html(hours + ':' + minutes + ':' + seconds);
            timer2 = hours + ' : ' + minutes + ' : ' + seconds;
        }, 1000);
    });
</script>
<div class="zrdn-discount">
	<p>
        <?php  _e('Like this plugin? The premium version includes lots of cool extra features, your 20% fast adapter discount is still valid!','zip-recipes') ?>
	</p>
	<h1><div class="countdown"></div></h1>
	<a target="_blank" href="https://ziprecipes.net/premium?discount={discount_code}" ><?php _e("check out premium","zip-recipes")?></a>
	&nbsp; <a target="_blank" href="https://ziprecipes.net/premium?discount={discount_code}" ><?php _e("apply discount","zip-recipes")?></a>
	&nbsp; <a id="zrdn-dismiss-discount" href="#"><?php _e("dismiss","zip-recipes") ?></a>
</div>