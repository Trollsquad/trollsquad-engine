<?php
if(!defined("FPINDEX") || FPINDEX!==true) die('Hacking attempt!');

$page['title'] = 'L-Projects';
$page['description'] = 'Осмысленные решения в области рекламы и digital.';
$page['keywords'] = '';
$social = array(
  'name'=>'L-Projects',
  'url'=>$config['SITE_URL'].$_SERVER['REQUEST_URI'],
  'image'=>'',
  'title'=>'L-Projects'
);
$popups = '
    <div id="js-popup_feedback" class="popup popup_form popup_feedback">
        <a class="popup__close"></a>
        <div class="popup__wrapper">
            <div class="popup__dialog">
                <div class="popup__content">

                    <form class="form form_feedback" method="post" action="ajax/mailer.php">

                        <label class="form__input size-xl">
                            <input class="js-vld-required" type="text" name="name" value="">
                            <span class="form__label">Имя</span>
                        </label>

                        <label class="form__input size-xl">
                            <input class="js-vld-required js-vld-phone" type="text" name="phone" value="">
                            <span class="form__label">Телефон</span>
                        </label>

                        <!--<span class="form__prefix">или</span>

                        <label class="form__input size-xl">
                            <input class="js-vld-required js-vld-email" type="text" name="mail" value="">
                            <span class="form__label">E-Mail</span>
                        </label>-->

                        <label class="form__textarea size-xl">
                            <textarea class="js-vld-required" name="message" cols="30" rows="4" maxlength="110"></textarea>
                            <span class="form__label">Сообщение</span>
                        </label>

                        <div class="form-footer">
                            <button class="btn size-xl" type="submit">Отправить</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <div id="js-popup_people" class="popup popup_quote popup_people">
        <a class="popup__close"></a>
        <div class="popup__wrapper">
            <div class="popup__background"></div>
            <div class="popup__dialog">
                <div class="popup__content"></div>
            </div>
        </div>
    </div>

    <div id="js-popup_about" class="popup popup_about">
        <a class="popup__close"></a>
        <div class="popup__wrapper">
            <div class="popup__dialog">
                <div class="popup__content">
                    <div class="popup__header">
                        <div class="company-logo">
                            L-Projects. Creative Agency.
                        </div>
                        <h1>
                            Осмысленные решения<br>
                            в области рекламы и digital
                        </h1>
                    </div>
                    <div class="popup__body">
                        <p><i>
                            Нам 11 лет!  За эти годы мы выполнили более 300 проектов, научились экономить ваше время,<br>
                            нервы и деньги, мыслить глобально и не зацикливаться на пустяках. Наши идеи и решения<br>
                            осмысленны и работают. Для нас нет секретов в рекламе!
                        </i></p>
                        <ul class="list list_withIcons">
                            <li class="list__item">
                                <span class="icon icon_digital"></span>
                                <h2>Digital</h2>
                                <p><i>Сайты и мобильные приложения</i></p>
                            </li>
                            <li class="list__item">
                                <span class="icon icon_polygraphy"></span>
                                <h2>Полиграфия</h2>
                                <p><i>Календари, каталоги, буклеты и т.п.</i></p>
                            </li>
                            <li class="list__item">
                                <span class="icon icon_design"></span>
                                <h2>Дизайн</h2>
                                <p><i>Графический дизайн, интерфейсы</i></p>
                            </li>
                            <li class="list__item">
                                <span class="icon icon_video"></span>
                                <h2>Видео</h2>
                                <p><i>Рекламные ролики, презентационные фильмы</i></p>
                            </li>
                            <li class="list__item">
                                <span class="icon icon_marketing"></span>
                                <h2>Маркетинг</h2>
                                <p><i>Исследования, аналитика, консалтинг</i></p>
                            </li>
                            <li class="list__item">
                                <span class="icon icon_souvenir"></span>
                                <h2>Сувениры</h2>
                                <p><i>Любая сувенирная продукция</i></p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="js-popup_map" class="popup popup_map">
        <a class="popup__close"></a>
        <div class="popup__wrapper">
            <div class="popup__dialog">
                <div id="js-popup__map-canvas" class="popup__content"></div>
            </div>
        </div>
    </div>

';
$result = $db->query("SELECT * FROM `ss_works` WHERE NOT (`slide`=0 OR `type`='') ORDER BY RAND() LIMIT 3");
$capt = '<div class="carousel__captions">';
$li = '<ul class="carousel__list" >';
$i = 0;
while ($row = $db->fetchrow($result)) {
  $s = ($i == 0) ? ' is-active' : '';
  $i++;
  $wimage = $db->fetchrow($db->query("SELECT `name`,`ext` FROM `ss_files` WHERE `id`='".$row['slide']."'"));
  $capt .= '
                        <div class="carousel__caption'.$s.'">
                            <a href="/works/'.$row['id'].'/"><span class="carousel__label">#'.$row['type'].'</span>'.$row['name'].'</a>
                        </div>';
  $li .= '
                        <li class="carousel__item'.$s.'" style="background-image: url(\''.$config['TEMPLATE_DIR'].'/img/default/alpha.png\')" data-img="./f/'.$wimage['name'].$wimage['ext'].'"> </li>';
}
$capt .='</div>';
$li .='</ul>';
$content = <<<HTML
           <header id="js-l-header" class="l-header l-header_adaptive l-header_top-186">

                <div class="company-logo-hor">
                    L-Projects. Creative Agency.
                </div>

                <nav id="js-nav_general" class="nav nav_general">
                    <div class="nav__category is-open">
                        <a class="nav__toggle" href="#"><span class="icon icon-dyn_menu js-icon-dyn"></span></a>
                        <ul class="nav__list">
                            <li class="nav__item"><a class="popup-toggle" data-name="about">Об агентстве</a></li>
                            <li class="nav__item"><a class="popup-toggle" data-name="map" data-data='{"lat": 55.7838493, "lng": 49.125927, "markerImg": "$config[TEMPLATE_DIR]/img/map_marker_01.png"}'>Контакты</a></li>
                        </ul>
                    </div>
                    <div class="nav__category">
                        <a class="popup-toggle" data-name="feedback"><span class="icon icon-dyn_mail js-icon-dyn"></span></a>
                    </div>
                    <div class="nav__category">
                        <a class="nav__toggle" href="#"><span class="icon icon-dyn_like js-icon-dyn"></span></a>
                        <ul class="nav__list">
                            <li class="nav__item"><a class="btn_share" data-type="fb">Facebook</a></li>
                            <li class="nav__item"><a class="btn_share" data-type="vk">Вконтакте</a></li>
                        </ul>
                    </div>
                </nav>

                <div class="company-data">
                    <span class="company-data__item">+7 843 233-00-00</span>
                    <span class="company-data__item">info@lprojects.ru</span>
                </div>

            </header>

            <div class="company-logo">
                L-Projects. Creative Agency.
            </div>

            <section id="js-box_carousel" class="box box_carousel">

                <div id="js-carousel_main" class="carousel carousel_fullscreen carousel_main">

                    <a class="carousel__nav-prev"></a>
                    <a class="carousel__nav-next"></a>

                    $capt
                    $li

                </div>

                <div class="logos">
                    <h2 class="title"># Клиенты</h2>
                    <ul class="logos__list">
                        <li class="logos__item"><a href="http://akbars.ru/" target="_blank"><img src="$config[TEMPLATE_DIR]/media/logo/logo_01_m.png" alt=""></a></li>
                        <li class="logos__item"><a href="http://tatspirtprom.ru/" target="_blank"><img src="$config[TEMPLATE_DIR]/media/logo/logo_02_m.png" alt=""></a></li>
                        <li class="logos__item"><a href="http://tatneft.ru/" target="_blank"><img src="$config[TEMPLATE_DIR]/media/logo/logo_03_m.png" alt=""></a></li>
                        <li class="logos__item"><a href="http://ak-bars.ru/" target="_blank"><img src="$config[TEMPLATE_DIR]/media/logo/logo_04_m.png" alt=""></a></li>
                    </ul>
                </div>

            </section>

            <section id="js-box_tiles" class="box box_tiles">

                <div id="js-teasers_tiles_main"></div>

            </section>

            <!--<section id="js-box_map" class="box box_map"></section>-->
HTML;

include_once($config['SITE_DIR'].$config['TEMPLATE_DIR'].'/header.php');
$globalCont .= $content;
include_once($config['SITE_DIR'].$config['TEMPLATE_DIR'].'/footer.php');
?>