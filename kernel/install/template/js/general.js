//-----------------------------------
//-----------------------------------
//----- 2013 L-Projects, Isotheos ------
//-----  Feel free to modify   ------
//-----------------------------------
//-----------------------------------


//----- GENERAL ENGINE ----
$(function() {
  
  getCustomInterface();
});

//---- Кастомизация элементов ----
function getCustomInterface() {
  $('input[placeholder],textarea[placeholder]').placeholder();
  $("input[name=phone],input.phone").mask("+7 999 999 9999",{placeholder:' '});
  
  // блокировка браузерной валидации форм и навешивание своих обработчиков
  $('form').each(function() {
    $(this).attr('onsubmit','onSubmit(this);').attr('novalidate','novalidate');
    $(this).find('input[type=submit],.submit').attr('onclick','onSubmit($(this).parents(\'form\'));return false;')
  });
  
  // чистка аттрибута for у лейблов
  $('label').each(function() {
    if ($(this).find('select').length > 0) {
      $(this).attr('for','notCreated');
    }
  });
  
  // замена пробелов на псевдопробелы для адекватной отработки text-overflow в select'ах
  $('select option').each(function() {
    $(this).text($(this).text().replace(' ',' '));
  });
  
  // замена select'ов
  var params = {
    changedEl: "select",
    visRows: 20,
    scrollArrows: true
  };
  cuSel(params);
  
  // замена радиобаттонов
  $('input[type=radio]:visible').each(function() {
    var id = $(this).attr('name')+'_'+mt_rand();
    var onchange = (typeof $(this).attr('onchange') !== 'undefined') ? $(this).attr('onchange') : '';
    if ($(this).parents()[0].tagName == 'LABEL') {
      var el = $(this).parents()[0];
      onchange = onchange.replace('this','$(this).children(\'div.cusRadio\')')+';';
      $(el).attr('onclick','radioRechange(\''+id+'\');'+onchange+'return false');
    }
      var dClass = ($(this).attr('checked') != undefined) ? 'cusRadio active' : 'cusRadio';
      $(this).after('<div id="'+id+'" value="'+$(this).attr('value')+'" name="'+$(this).attr('name')+'" class="'+dClass+'"></div>');
      if ($(this).parents()[0].tagName != 'LABEL') $('#'+id).attr('onclick','radioRechange(\''+id+'\');'+onchange);
      $(this).hide();    
  });
  // замена чекбоксов
  $('input[type=checkbox]:visible').each(function() {
    var id = $(this).attr('name')+'_'+mt_rand();
    var onchange = (typeof $(this).attr('onchange') !== 'undefined') ? $(this).attr('onchange') : '';
    if ($(this).parents()[0].tagName == 'LABEL') {
      var el = $(this).parents()[0];
      var pClass = ($(this).attr('checked') != undefined) ? 'active' : '';
      onchange = onchange.replace('this','$(this).children(\'div.cusCheckbox\')')+';';
      $(el).attr('onclick','checkboxRechange(\''+id+'\');'+onchange+'return false').addClass(pClass);
    }
    var dClass = ($(this).attr('checked') != undefined) ? 'cusCheckbox active' : 'cusCheckbox';
    $(this).after('<div id="'+id+'" value="'+$(this).attr('value')+'" name="'+$(this).attr('name')+'" class="'+dClass+'"></div>');
    if ($(this).parents()[0].tagName != 'LABEL') $('#'+id).attr('onclick','checkboxRechange(\''+id+'\');'+onchange);
    $(this).hide();
  });
}

//---- Обработка кастомных элементов формы ----
// клик по кастомному радиобаттону
function radioRechange(id) {
  var el = $('#'+id);
  var par = (el.parents('form').length == 0) ? el.parent('div') : el.parents('form');
  if (el.hasClass('active')) return false;
  var name = el.attr('name');
  var value = el.attr('value');
  par.find('input[name='+name+']').removeAttr('checked');
  par.find('input[name='+name+'][value='+value+']').attr('checked','checked');
  par.find('div[name='+name+']').removeClass('active');
  par.find('div[name='+name+'][value='+value+']').addClass('active');
}

// клик по кастомному чекбоксу
function checkboxRechange(id) {
  var el = $('#'+id);
  var name = el.attr('name');
  if (el.hasClass('active')) {
    $('input[name='+name+']').removeAttr('checked');
    $('input[name='+name+']').parent('label').removeClass('active');
    $('div[name='+name+']').removeClass('active');
  }
  else {
    $('input[name='+name+']').attr('checked','checked');
    $('div[name='+name+']').addClass('active');
    $('input[name='+name+']').parent('label').addClass('active');
  }
}


//---- Модальные Окна v2.1 ----
// создание нового окна
function getModal(content,callback) {
  $('body').append('<div class="modal"></div>')
	var scroll = $(window).scrollTop() + ($(window).height() / 2);
	$('.modal:last').html('<div class="mClose" onclick="modalHide()"></div><div class="mText">'+content+'</div>').removeAttr('style').css({display:'block',opacity:0})
	$('.modal:last').css({top:scroll});
  $('.modal:last').css({marginLeft:- ($('.modal:last').width() / 2),marginTop:-$('.modal:last').height() / 2});
  if ($('.modalOverlay:visible').length == 0) { $('.modalOverlay').attr('onclick','modalHide()').css({opacity:0,display:'block'}).animate({opacity:.8},200);}
  $('.modal:last').css({marginLeft:- ($('.modal:last').width() / 2),marginTop:-$('.modal:last').height() / 2}).animate({opacity:1},200);
  getCustomInterface();
  if (callback) callback();
}
// уничтожение окна
function modalClose() {
	if ($('.modal').length == 1) $('.modalOverlay:last').hide();
	$('.modal:last').removeAttr('class').addClass('modal').empty().hide().remove();
}
// плавное скрытие окна
function modalHide() {
	if ($('.modal').length == 1) $('.modalOverlay:last').animate({opacity:0},600);
	$('.modal:last').animate({opacity:0},600,function() {
		modalClose();
	});
}

//---- Кастомная валидация форм ----
function onSubmit(el) {
  var textareas = $(el).find('textarea');
  var select = $(el).find('select');
  var res = true;
  $(el).find('input,textarea').each(function() {
    var pat = true;
    if (typeof $(this).attr('pattern') != 'undefined') {
      var reg = $(this).attr('pattern');
      if ($(this).val().search(reg) == -1) pat = false;
    }
    if (!pat || (typeof $(this).attr('required') != 'undefined' && $(this).val().length < 1) || ($(this).attr('type') == 'email' && !testinput(/[a-z0-9A-Z-_]@[a-z0-9A-Z-_].[a-zA-Z]{1,5}/,$(this).val())) || ($(this).attr('type') == 'time' && !testinput(/[0-9]{1,2}\:[0-9]{1,2}/,$(this).val()))) {
      res = false;
      $(this).addClass('error').attr('onkeyup','removeError(this);');
    }
  });
  if ($(el).find('input[type=password]').length == 2) {
    var val = false;
    $(el).find('input[type=password]').each(function() {
      if (val != false && val != $(this).val()) {
        res = false;
        $(this).addClass('error').attr('onkeyup','$(this).removeClass(\'error\')');
      }
      else {
        val = $(this).val();
      }
    });
  }
  if (res) el.submit();
}
// удаление ошибок
function removeError(e) {
  $(e).removeClass('error');
  $(e).removeAttr('onkeyup');
}

//---- MISK ----
// сравнение регулярных выражений
function testinput(re, str){
  return (str.search(re) != -1);
}

// php like рандомизатор
function mt_rand(min,max) {
  var argc = arguments.length;
  if (argc === 0) {
    min = 0;
    max = 2147483647;
  }
  else if (argc === 1) {
    throw new Error('Warning: mt_rand() expects exactly 2 parameters, 1 given');
  }
  else {
    min = parseInt(min, 10);
    max = parseInt(max, 10);
  }
  return Math.floor(Math.random() * (max - min + 1)) + min;
}

function checkBD() {
  var bd_name = $('input[name=bd_name]').val();
  var bd_user = $('input[name=bd_user]').val();
  var bd_pass = $('input[name=bd_pass]').val();
  $('#checkbd').removeClass('pink green');
  $.ajax({
    type: "GET",
    url: 'checkbd.php',
    data: 'bd_name='+bd_name+'&bd_user='+bd_user+'&bd_pass='+bd_pass,
    success: function(msg) {
      msg = eval('('+msg+')');
      if (msg.status == 'true') {
        $('#checkbd').addClass('green').attr('title',msg.mess);
      }
      else if (msg.status == 'error') {
        $('#checkbd').addClass('pink').attr('title',msg.mess);
      }
    }
  });
}
function checkModules() {
  var module_portfolio = $('input[name=module_portfolio]').attr('checked');
  var module_forms = $('input[name=module_forms]').attr('checked');
//  console.log(module_portfolio+'; '+module_forms)
  if (module_forms == 'checked' && $('.mod_forms').length == 0) {
    $('input[name=module_forms]').parents('label').after('\
      <div class="mod_forms">шмякать</div>\
    ');
  }
  if (typeof module_form == 'undefined' && $('.mod_forms').length == 1) {
    console.log('Уничтожаем форму для форм')
  }
  if (typeof module_portfolio == 'undefined' && $('.mod_portfolio').length == 1) {
    console.log('Уничтожаем форму для портфолио')
  }
  if (module_portfolio == 'checked' && $('.mod_portfolio').length == 0) {
    $('input[name=module_portfolio]').parents('label').after('\
      <div class="mod_portfolio">шмякать</div>\
    ');
  }
}