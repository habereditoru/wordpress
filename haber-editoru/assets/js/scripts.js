jQuery(document).ready(function($) {
 
	$("#plugin-information-tabs a").click(function() {
 
        $("#plugin-information-tabs a").removeClass('current');
        $(this).addClass('current');
 
        $.ajax({ url: this.href, success: function(html) {
            $("#ajax-content").empty().append(html);
            }
		});
		return false;
    });
	
	// genel ayarlar kayıt
	$('#he-form-ayarlar').submit(function(event){
		
		$("#ga_submit").val("Bekleyiniz...");
		
		postveri($('#he-form-ayarlar').serialize() + '&action=he_query' , function(verib){
			if (verib == "RES"){
				window.location.reload();
			} else {
				$(".ajaxMsg").html(verib);
				$("#ga_submit").val("Kaydet");
			}
		});
		
		event.preventDefault();
		
	});

	$("#ajanslar_ids input").click(function(){
		id = this.id;
		var kats = "fs_"+id;
		console.log(kats);
		if (this.checked==true){
			$("#"+kats).css("display","");
		}else{
			$("#"+kats+" input:checkbox:checked").each(function(){ $(this).removeProp( "checked" ); });
			$("#"+kats).css("display","none");
		}
	});
	
	
	// kategori eslestirme kayıt

	$('#he-form-ke').submit(function(event){
		$(".ajaxMsg").html('');
		$("#ke_submit").val("Kaydediliyor");

		postveri($('#he-form-ke').serialize() + '&action=he_query', function(verib){
			verib = verib.trim();
			if (verib == "RES"){
				window.location.reload();
			}else {
				$(".ajaxMsg").html(verib);
				$("#ke_submit").val("Kaydedildi");
			}
			console.log(verib);
			$("#ke_submit").val("Kaydedildi");
			setTimeout(function(){$("#ke_submit").val("Kaydet");}, 1000);
		});
		event.preventDefault();
	});
	
	
	
	// bot ayarlar kayıt
	$('#he-form').submit(function( event  ){
		
		$("#ba_submit").val("Bekleyiniz...");
		
		var ajanslar_str = "" ;
		var kategoriler_str  ="" ;
		$("#ajanslar_ids input:checkbox:checked").each(function(){ ajanslar_str += $(this).attr("data-name")+","; });
		ajanslar_str = ajanslar_str.substring(0, (ajanslar_str.length-1));
		$("#kategoriler_ids input:checkbox:checked").each(function(){ kategoriler_str += $(this).attr("data-name")+","; });
		kategoriler_str = kategoriler_str.substring(0, (kategoriler_str.length-1));
		//alert( ajanslar_str );
		
		postveri($('#he-form').serialize()+"&ajanslar_str="+ajanslar_str+"&kategoriler_str="+kategoriler_str + '&action=he_query', function(verib){
			if (verib == "RES"){
				window.location.reload();
			} else {
				$(".ajaxMsg").html(verib);
				$("#ba_submit").val("Kaydedildi");
			}
		});
		
		event.preventDefault();
	});

	// bot görüntüleme bölümü - etkinleştir tekil düğme.
	$("a[id*='btn_durum_']").click(function(){
			var ida = this.id.split("_");
			var id = ida[2];
			var strval = $(this).attr("strval");
			var aktif = $(this).attr("aktif");
					
			if (strval == ""){
				alert("Lütfen robot ayarlarını kontrol ediniz.");
				return false;
			}
			
			botstatechange(id, aktif, strval);



	});
	
	// bot görüntüleme bölümü - etkinleştir tümüne uygula.
	$("#doaction").click(function(){

		$("#the-list input:checkbox:checked").each(function(){
		
			var aktif = $("#bulk-action-selector-top").val();
		
			var ida = this.id.split("_");
			var id = ida[2];
			var strval = $("#btn_durum_"+id).attr("strval");	
			
			if (strval != ""&&(aktif=="0"||aktif=="1")){
				botstatechange(id, aktif, strval, "1");
			}

		});
	
	});
	
	// bot görüntüleme bölümü işlem fonksiyonu
	function botstatechange(id, aktif, strval, tip){

			if (tip=="1"){
				if (aktif == "1"){aktif="0";}else{aktif="1";}
			}
	
			var mesaj = "";
			var Naktif = "";
			var clssA = "";
			var clssR = "";
			
			$("#btn_durum_"+id).html("Bekleyiniz...");

			var veri = "tip=ba_e&bot="+id;
			if (aktif == "1"){
				Naktif = "0";
				mesaj = "<span class='dashicons dashicons-controls-play'></span> Başlat";
				clssA = "inactive";
				clssR = "active";
			}else{
				Naktif = "1";
				mesaj = "<span class='dashicons dashicons-controls-pause'></span> Durdur";
				clssA = "active";
				clssR = "inactive";
			}
			veri += "&aktif="+Naktif;
			
			postveri(veri, function(verib){
				if (verib == "RES"){
					window.location.reload();
				}
				console.log(verib);
				$("#btn_durum_"+id).attr("aktif", Naktif);
				$("#robot"+id).addClass( clssA );
				$("#robot"+id).removeClass( clssR );
				setTimeout(function(){
						$("#btn_durum_"+id).html(mesaj);
					}, 1000);
			});

	}
	
	
	// abonelik kayıt
	$('#he-form-register').submit(function(event){
		$(".ajaxMsg").html('');
		$("#ab_submit").val("Bekleyiniz...");

		postveri($('#he-form-register').serialize() + '&action=he_query', function(verib){
			verib = verib.trim();
			if (verib == "RES"){
				window.location.reload();
			}else {
				$(".ajaxMsg").html(verib);
				$("#ab_submit").val("Kaydedildi");
			} 
			console.log(verib);
			$("#ab_submit").val("Kaydedildi");								
			setTimeout(function(){$("#ab_submit").val("Kaydet");}, 1000);
		});
		event.preventDefault();
	});
	


   function postveri(veri, callback) {
		$.ajax({
			type: "POST",
			url: he.ajax_url,
			data: veri,
			success: function (msg) {
				var data = msg.hasOwnProperty("d") ? msg.d : msg;
				if (callback)
					callback(data);
			}
		});
	}

});

function ajaxPost(form,to){
if ( console && console.log ){console.log(form + "--" + to);}
$('#' + to).html('<img src="../img/loading.gif" />');
$.ajax({type:'POST',url:he.ajax_url,data:$('#'+form).serialize()})
	.done(function(data) {
		$('#' + to).html(data) ;
	})
	.fail(function() {
		alert( "Bir Hata Oluştu!\nLütfen tekrar deneyin..." );
	});
}

function ajaxGet(form,to){
if ( console && console.log ){console.log(form + "--" + to);}
$('#' + to).html('<img src="../img/loading.gif" />');
$.ajax({type:'POST',url:he.ajax_url+"?"+form})
	.done(function(data) {
		$('#' + to).html(data) ;
	})
	.fail(function() {
		alert( "Bir Hata Oluştu!\nLütfen tekrar deneyin..." );
	});
}


function ajaxLink(href,to){
if ( console && console.log ){console.log(href + "--" + to);}
$('#' + to).html('<img src="../img/loading.gif" />');
$.ajax({type:'GET',url:href,cache :false})
	.done(function(data) {
		$('#' + to).html(data) ;
	})
	.fail(function() {
		alert( "Bir Hata Oluştu!\nLütfen tekrar deneyin..." );
	});
}

function dildegistir(oncesi,sonrasi){
	if (oncesi != sonrasi){
		alert("Dil değiştirseniz kaynak ve kategoirler de değişecektir. Yeni içerik kaynak ve kategorileri görmek için KAYDET düğmesenize basınız...");
		document.getElementById('ajanslar_ids').style.display="none";
		document.getElementById('kategoriler_ids').style.display="none";
	}else{
		document.getElementById('ajanslar_ids').style.display="";
		document.getElementById('kategoriler_ids').style.display="";	
	}
}

function show_id(id){

	for (var i=1;i<=250;i++){
		if (document.getElementById(id+i)){
			if (document.getElementById(id+i).style.display=="none"){
				document.getElementById(id+i).style.display="";
				break;
			}
		}
	}
}

function remove_id(id){
var element = document.getElementById(id);
element.parentNode.removeChild(element);

}

