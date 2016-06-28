<?php
defined('_JEXEC') or die;

$doc = JFactory::getDocument();
$doc->addScriptDeclaration('');
$app = JFactory::getApplication();
$menu = $app->getMenu();

function getIp() {
	if (!empty($_SERVER['HTTP_CLIENT_IP'])){
		$ip=$_SERVER['HTTP_CLIENT_IP'];
	}elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
		$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	}else{
		$ip=$_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}
$ip = getIp();


?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >
<head>

	<script type="text/javascript" src="http://cdn.jsdelivr.net/jquery/1.11.0/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/jquery.fancybox.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/jquery.fancybox.css?v=2.1.5" media="screen" />
	<jdoc:include type="head" />
	
		<?php
		unset(
			//$this->_scripts[$this->baseurl.'/media/system/js/mootools-core.js'],
			//$this->_scripts[$this->baseurl.'/media/system/js/mootools-more.js'],
			//$this->_scripts[$this->baseurl.'/media/system/js/core.js'],
			//$this->_scripts[$this->baseurl.'/media/system/js/caption.js'],
			$this->_scripts[$this->baseurl.'/media/jui/js/jquery.min.js'],
			$this->_scripts[$this->baseurl.'/media/jui/js/jquery-migrate.min.js']
									
		);	
		
	
	?>
	<meta charset="UTF-8">
	
	
	<!--[if IE ]>

		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script> 
	<![endif]-->
	<!--[if lt IE 7]>
		<script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE7.js"></script>
	<![endif]-->
	<!--[if lte IE 8]>
		<link rel="stylesheet" href="css/style_ie.css" type="text/css" />
		<script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE8.js"></script>
	<![endif]-->
	<!--[if lt IE 9]>
		<script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
	<![endif]-->
	
	

	

	<link href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/template.css" type="text/css" rel="stylesheet">
		

	<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/template.js"></script>
	<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/jquery.inputmask.js"></script>
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic&amp;subset=latin,cyrillic" rel="stylesheet" type="text/css">	
	<link href='https://fonts.googleapis.com/css?family=Fira+Sans&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
	
	<link href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/normalize.css" type="text/css" rel="stylesheet">
	<link href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/header-footer.css" type="text/css" rel="stylesheet">
	
	<?php if($_SERVER['REQUEST_URI'] != '/'){?>
		<link href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/general.css" type="text/css" rel="stylesheet">
	<?php } ?>
</head>
<body>

		<header class="header">
			<div class="center-box">
				<div class="logotip">
					<a href="/" class="logo-icon"></a>
					<div class="text">
						<p>Официальный центр продажи квартир в новостройках в Реутове</p>
						<span>при поддерже компании Миэль</span>
					</div>
				</div>
				<div class="links">
					<nav class="main-menu">
						<jdoc:include type="modules" name="top_menu" />

					</nav>
					<div class="block-btn">
						<div class="block-btn-links">
							<a href="tel:+74955041595" class="tel"><span>+7 (495)</span> 504 15 95</a>
							<small>Пн-Пт:  9.00 - 20.00,   Сб-Вс: 10.00 - 18.00</small>
						</div>
						<a href="#call_me" class="open-modal" title="обратный звонок">Позвонить Вам</a>
					</div>
				</div>
			</div>
		</header>
<?php if ($this->countModules('menu')){ ?>
<div class="website-menu mw">
	<div class="center-box">
		<jdoc:include type="modules" name="menu" />	
	</div>
</div> 		
<?php } ?>

<?php if ($this->countModules('y-line')){ ?>
		<jdoc:include type="modules" name="y-line" />	
<?php } ?>

<?php if ($this->countModules('breadcrumbs')){ ?>
<div class="crumbs mw">
	<div class="center-box">
		<jdoc:include type="modules" name="breadcrumbs" />		
	</div>
</div>		
<?php } ?>			

	<jdoc:include type="message" />
	<jdoc:include type="component" />	
	
		<footer class="footer mw">
			<div class="center-box">
				<div class="left">
					<?php if ($this->countModules('footer_menu')){ ?>
						<jdoc:include type="modules" name="footer_menu" />	
					<?php } ?>
					<p>© Официальный центр продажи квартир в новостройках в Реутове. </p>
					<p>При использовании материалов ссылка на сайт обязательна.</p>
				</div>
				<div class="footer-links">
					<a href="tel:+74955041595"><span>+7 (495)</span> 504 15 95</a>
					<p>Пн-Пт:  9.00 - 20.00,&nbsp;&nbsp;Сб-Вс: 10.00 - 18.00</p>
				</div>
			</div>
		</footer>
<div id="call_me" class="molod" style="display: none;" >
<div class="wrapper perezvonite_mne">
		
		<div class="modal_body">	
			<p class="title">Мы всегда рады вам помочь</p>
			<p class="desc">Оставьте нам свои координаты - и мы с вами свяжемся в ближайшее время.</p>
			<form method="POST" id="form_perezvonite_mne" action="javascript:void(null);" onsubmit="submit_form(this)">

				<input type="hidden" name="url" value="<?php echo $_SERVER['REQUEST_URI'];?>">				
									<div class="form_div">
										<label>Имя<span class="red">*</span></label>
										<input type="text" class="validation" name="name" vk_14a48="subscribed">
									</div>
									<div class="form_div">
										<label>Телефон<span class="red">*</span></label>																				
										<input type="text" class="validation phone" name="phone" vk_14a48="subscribed">
									</div>
									<div class="form_div">
										<label>Удобное время для звонка</label>
										<input type="text" name="time_to_call" vk_14a48="subscribed">
									</div>
									<div class="form_div">
										<input type="checkbox" name="call_me_now" value="1"><span class="call">Позвоните мне прямо сейчас</span>
									</div>
									<div class="form_div">
										<p class="notice"><span class="red">*</span> Обязательные для заполнения поля</p>
										<input type="submit" value="Отправить">
									</div>
			</form>
			<div style="clear:both;"></div>
		</div>
	</div>
</div>
<!-- --------------- forma -->
<a class="form_anim_button">
		<img class="form_anim_button_visible" src="templates/weeb/images/quick_call_button_1.png" alt="call" />
		<img style="display: none;" src="templates/weeb/images/quick_call_button_2.png" alt="call" />
		<img style="display: none;" src="templates/weeb/images/quick_call_button_3.png" alt="call" />
		<img style="display: none;" src="templates/weeb/images/quick_call_button_4.png" alt="call" />
</a>
<div class="form_anim">
	<a class="form_anim_close"></a>
	<a class="form_anim_triangle"></a>
	<div class="form_anim_icons">
		<a class="form_anim_call_icon form_anim_selected"><div class="icon_anim_call"></div>Звонок</a>
		<a class="form_anim_mail_icon"><div class="icon_anim_mail"></div>Письмо</a>
	</div>
	
	<table id="form_call_anim" cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<form action="javascript:void(null);" id="form_call_anim_phone" onsubmit="submit_form(this);">
					<input type="hidden" name="title" value="Обратный звонок" />
					<input type="hidden" name="url" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
					<div id="form_call_anim_text"></div>
					<span class="cod-v-phone">+7</span><input type="tel" name="phone" class="v-phone form_call_anim_phone validation need-int" placeholder="Введите ваш номер" value="" required />
					
					<select name="time_to_call" id="form_later_anim_day">
							<option value="Сегодня">Сегодня</option>
							<option class="tomorrow" value=""></option>
							<option class="after-tomorrow" value=""></option>
					</select>

					<div class="form_later_anim_selects_text">в</div>
					
					<input type="text" name="time_today" id="time_today" placeholder="18:00" value="18:00" />
					
					<div class="block_call_me_now">
						<input type="checkbox" name="form_call_me_now" class="form_call_me_now" />					
						<span class="call_me_now">Позвоните мне прямо сейчас</span>
					</div>
					
					<input type="submit" name="submit_form_call_anim" value="Жду звонка!" />
				</form>

				
				
			</td>
		</tr>
	</table>

	<table id="form_mail_anim" cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<div id="form_mail_anim_text" class="form_mail_anim_hide"></div>
				<form class="form_mail_anim_hide" action="javascript:void(null);" id="form_mail_anim_hide" onsubmit="submit_form(this)">
					<input type="hidden" name="title" value="Задать вопрос, написать письмо" />
					<input type="hidden" name="url" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
					<textarea name="text" class="form_mail_anim_question validation" rows="" cols="5" placeholder="Напишите вопрос" required ></textarea>
					<input type="email" name="email" class="form_mail_anim_email validation" placeholder="Ваш e-mail (для ответа)" value="" required />
					<input type="text" name="phone" class="form_mail_anim_phone validation phone" placeholder="Ваш телефон (по желанию)" value="" required />
					<input type="submit" name="submit_form_mail_anim" value="Отправить" />
				</form>

				<div class="success_form_mail_anim"></div>
			</td>
		</tr>
	</table>

</div>

<script type="text/javascript">

var i = 0;
var j = 0;
var k = 0;

/* ------------------------- */

var text_1 = '<h3>У вас есть вопросы?</h3>Наш менеджер перезвонит <br>вам в удобное время!';
function type_call(){
	i++;
	if( i <= text_1.length )
		document.getElementById("form_call_anim_text").innerHTML = text_1.substr(0, i) + "_";
	else
		return false;
	
	setTimeout( type_call, 60 );
}

/* ------------------------- */

var text_2 = 'Выберите удобное <br />время звонка';
function type_later(){
	j++;
	if( j <= text_2.length )
		document.getElementById("form_later_anim_text").innerHTML = text_2.substr(0, j) + "_";
	else
		return false;
	
	setTimeout( type_later, 60 );
}

/* ------------------------- */

var text_3 = '<h3>Напишите ваши вопросы!</h3>Мы обязательно оперативно<br />на них ответим!';
function type_mail(){
	k++;
	if( k <= text_3.length )
		document.getElementById("form_mail_anim_text").innerHTML = text_3.substr(0, k) + "_";
	else
		return false;
	
	setTimeout( type_mail, 60 );
}

/* --------------------------------------------------------------------------------- */

(function($) {

/* ------------------------- */

$(document).ready(function() {
	function echo_date( date ){
var months = ["январь","феврать","март","апрель","май","июнь","июль","август","сентябрь","октябрь","ноябрь","декабрь"];   
    echo_date = function(date){
        date = new Date( date );
        return {
            "date" : date,
            "month" : months[ date.getMonth() ],
            "day_num" : date.getDate()
        };
    }
    return echo_date(date);   
};

var tomorrow = echo_date( Date.now()+24*60*60*1000 );
var after_tomorrow = echo_date( Date.now()+(24*60*60*1000)*2 );

$('#form_call_anim .tomorrow').val(tomorrow.day_num+" "+tomorrow.month);
$('#form_call_anim .after-tomorrow').val(after_tomorrow.day_num+" "+after_tomorrow.month);
$('#form_call_anim .tomorrow').text(tomorrow.day_num+" "+tomorrow.month);
$('#form_call_anim .after-tomorrow').text(after_tomorrow.day_num+" "+after_tomorrow.month);

		/*$('#form_call_anim input:submit').click(function(){
				$.post("form_anim/form_call_anim_send.php", $("#form_call_anim form").serialize(),  function(response) {
						$('.success_form_call_anim').html(response);
				});
				return false;
		});
		
		$('#form_later_anim input:submit').click(function(){
				$.post("form_anim/form_later_anim_send.php", $("#form_later_anim form").serialize(),  function(response) {
						$('.success_form_later_anim').html(response);
				});
				return false;
		});
		
		$('#form_mail_anim input:submit').click(function(){
				$.post("form_anim/form_mail_anim_send.php", $("#form_mail_anim form").serialize(),  function(response) {
						$('.success_form_mail_anim').html(response);
				});
				return false;
		});*/
		
		/* ------------------------- */
		
		function form_anim_show() {
				$('#form_call_anim_text').text('');
				$('#form_later_anim_text').text('');
				$('#form_mail_anim_text').text('');
				$('.form_anim_mail_icon').removeClass('form_anim_selected');
				$('.form_anim_call_icon').addClass('form_anim_selected');
				$(".form_anim").animate({
						marginRight: "0"
				}, 250, function() {
						// Animation complete
							$('#form_call_anim').show();
						});
				/*
				$("#кнопка звонка").animate({
						marginBottom: "-29px"
				}, 250 );
				*/
		}
		
			
		$(".form_anim_button, .open-modal").click(function(event){
			event.preventDefault();
				if ( typeof (form_anim_timer) != 'undefined' )
						clearTimeout(form_anim_timer);
				form_anim_show();
		});
		
		$(".form_anim_triangle, .form_anim_close").click(function(){
				$(".form_anim").animate({
					marginRight: "-355px"
				}, 250, function() {
					// Animation complete
												$('#form_call_anim').show();
									});
				/*
				$("#кнопка звонка").animate({
					marginBottom: "0"
				}, 250 );
				*/
		});
		
		/* ------------------------- */
		
		$('#form_call_anim_later').click(function() {
				$('#form_later_anim').show();
		});
		
		/* ------------------------- */
		
		$('.form_anim_call_icon').click(function() {
				if ( $(this).hasClass('form_anim_selected') )
						return false;
				$('.form_anim_mail_icon').removeClass('form_anim_selected');
				$(this).addClass('form_anim_selected');
										$('#form_call_anim').show();
						});
		
		$('.form_anim_mail_icon').click(function() {
				if ( $(this).hasClass('form_anim_selected') )
						return false;
				$('.form_anim_call_icon').removeClass('form_anim_selected');
				$(this).addClass('form_anim_selected');
				$('#form_mail_anim').show();
		});
		
		/* ------------------------- */
		
		/*$('#form_later_anim_day').click(function() {
			if ( $(this).val() == 'Сегодня' ) {
					$('#time_today').css('display','inline');
					$('#time_tomorrow').css('display','none');
			}
			else {
					$('#time_today').css('display','none');
					$('#time_tomorrow').css('display','inline');
			}
		}).click();
		*/
		/* ------------------------- */
		
		function setSrc() {
				var id_first = $('.form_anim_button img:first');
				var id_last = $('.form_anim_button img:last');
				var id_selected = $('.form_anim_button_visible');
				var to_first = 0;
				if ( $(id_last).hasClass('form_anim_button_visible') ) {
					to_first = 1;
				}
				$('.form_anim_button img').removeClass('form_anim_button_visible');
				$('.form_anim_button img').css('display', 'none');
				if ( to_first == 1 ) {
					$(id_first).addClass('form_anim_button_visible');
				}
				else {
					$(id_selected).next().addClass('form_anim_button_visible');
				}
				$('.form_anim_button_visible').css('display', 'inline');
		}
		setSrc_interval_id = setInterval(function() {
			setSrc();
		}, 2000);
		
}); // ready

/* ------------------------- */

$.each(['show', 'hide'], function (i, ev) {
		var el = $.fn[ev];
		$.fn[ev] = function () {
				this.trigger(ev);
				return el.apply(this, arguments);
		};
});

/* ------------------------- */

$('#form_call_anim').on('show', function() {
		$('#form_later_anim').hide();
		$('#form_mail_anim').hide();
		$('#form_call_anim_text').text('');
		i = 0;
		j = 0;
		k = 0;
		type_call();
});

$('#form_later_anim').on('show', function() {
		$('#form_call_anim').hide();
		$('#form_mail_anim').hide();
		$('#form_later_anim_text').text('');
		i = 0;
		j = 0;
		k = 0;
		type_later();
});

$('#form_mail_anim').on('show', function() {
		$('#form_call_anim').hide();
		$('#form_later_anim').hide();
		$('#form_mail_anim_text').text('');
		i = 0;
		j = 0;
		k = 0;
		type_mail();
});

/* ------------------------- */

})(jQuery);
function open_modal(){
			if ( typeof (form_anim_timer) != 'undefined' )
						clearTimeout(form_anim_timer);
				$('#form_call_anim_text').text('');
				$('#form_later_anim_text').text('');
				$('#form_mail_anim_text').text('');
				$('.form_anim_mail_icon').removeClass('form_anim_selected');
				$('.form_anim_call_icon').addClass('form_anim_selected');
				$(".form_anim").animate({
						marginRight: "0"
				}, 250, function() {
						// Animation complete
							$('#form_call_anim').show();
						});
		}
</script>
<!-- --------------- end forma -->
<!-- Yandex.Metrika counter -->
<script type="text/javascript">
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function() {
            try {
                w.yaCounter38057075 = new Ya.Metrika({
                    id:38057075,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true,
                    webvisor:true,
                    trackHash:true
                });
            } catch(e) { }
        });

        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function () { n.parentNode.insertBefore(s, n); };
        s.type = "text/javascript";
        s.async = true;
        s.src = "https://mc.yandex.ru/metrika/watch.js";

        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else { f(); }
    })(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/38057075" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->

</body>
</html>