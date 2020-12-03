/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    "Eleadtech_ProductSlider/js/swiper-bundle.min",
    'jquery',
    'mage/translate',
    'mage/template',
    "mage/mage",
    'Magento_Catalog/js/validate-product',
    "Magento_Swatches/js/swatch-renderer",
    "mage/gallery/gallery"
], function(Swiper, $, $t,mageTemplate,mage, productValidate) {
    "use strict";
    var productSliderSwiper = null;
    var sliderFlag = false;
    var allowWidth = 768;
    return {
        destroy: function(deleteInstance, cleanStyles){
            if(productSliderSwiper !=null){
                productSliderSwiper.destroy(deleteInstance,cleanStyles);
                this.removeSliderCategoryStatus();
            }
        },
        getProductItemObjectList: function(){
            return $("#maincontent .products .product-item");
        },
        addSliderIcon: function(){
            this.getProductItemObjectList().each(function(){
                $(this).find(".slider_icon").remove();
                if($(window).width() >= allowWidth){
                    $(this).append("<div class='slider_icon'></div>");
                }
            });
            if($(window).width() >= allowWidth) {
                var _self = this;
                this.getProductItemObjectList().find(".slider_icon").each(function (index) {
                    $(this).unbind("click").bind("click", function (event) {
                        event.preventDefault();
                        _self.initSlider(index + 1);
                    });
                });
            }
        },
        start: function(useTemplateForLoading){
            var _self = this;
            $(window).resize(function(){
                if(productSliderSwiper != null && productSliderSwiper != undefined){
                    _self.destroy(true,true);
                }
                _self.init(useTemplateForLoading);
            });
            _self.init(useTemplateForLoading);
        },
        init: function(useTemplateForLoading){
            var _self = this;
            if(useTemplateForLoading){
                this.createSliderTemplate();
            }
            this.addSliderIcon();
        },
        initSlider: function(index){
            var _self = this;
            sliderFlag = false;
            this.applySliderHeight();
            productSliderSwiper = new Swiper('.swiper-container', {
                slidesPerView: 1,
                spaceBetween: 0,
                slidesPerGroup: 1,
                loop: true,
                loopFillGroupWithBlank: true,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                on:{
                    transitionEnd: function (swiper){
                        var url = _self.getProductUrl(swiper.realIndex);
                        console.log(url);
                        console.log(sliderFlag);
                        if(url != undefined && url != "" && url != false && sliderFlag==true){
                            _self.loadProduct(swiper,url);
                        }
                    },
                    init: function(swiper){
                        swiper.slideTo(index);
                        var slideIndex  = index-1;
                        var url = _self.getProductUrl(slideIndex);
                        if(url != undefined && url != "" && url != false && sliderFlag==false){
                            _self.loadProduct(swiper,url);
                        }
                    }
                }
            });
            this.initDestroySlider();
        },
        initDestroySlider: function(){
            var _self = this;
            $(".product-slider-container .close_button").unbind("click").bind("click",function(event){
                _self.destroy(true,true);
            });
        },
        loadProduct: function(swiper,url){
            var _self = this;
            $.ajax({
                url: url,
                type: 'post',
                dataType: 'json',
                data: {isAjax: 1},
                success: function(res) {
                    if(res.error == 0){
                        _self.addSliderContent(res.data, swiper.realIndex);
                    }
                    else{
                        console.log("Cannot Load Slider");
                        console.log(res.message);
                        _self.destroy(false,true);
                    }
                },
                error: function(){
                    _self.destroy(false,true);
                }
            }).done(function(){

            });
        },
        getProductUrl: function (slideIndex){
            var url = false;
            var dataSliderIndex =  '[data-swiper-slide-index="' + slideIndex.toString() + '"' +"]";
            if($(".product-slider-container").find(dataSliderIndex).children("span.hidden")){
                url = $(".product-slider-container").find(dataSliderIndex).children("span").html();
            }
            return url;
        },
        addSliderContent: function (data,slideIndex){
            try{
                var dataSliderIndex =  '[data-swiper-slide-index="' + slideIndex.toString() + '"' +"]";
                $(".product-slider-container").find(dataSliderIndex).html(data);
                $(dataSliderIndex).trigger('contentUpdated');
                sliderFlag = true;
            }
            catch (ex){
                console.log(ex);
                alert("aaaaa");
            }
        },
        hideCategoryContent: function(){
            $("#maincontent .columns").hide();
        },
        showCategoryContent: function(){
            $("#maincontent.columns").show();
        },
        getSliderHeight(){
            var windowWidth = $(window).width();
            var sliderHeight = "1000px";
            for (var screenWidth in window.screenFormula) {
                if(windowWidth >= screenWidth){
                    sliderHeight = window.screenFormula[screenWidth];
                }
            }
            return sliderHeight;
        },
        applySliderHeight:function(){
            $(".product-slider-container").css("height",this.getSliderHeight() + "px");

            this.applySliderCategoryStatus();
        },
        applySliderCategoryStatus:function()  {
            this.toggleCategoryTitle("hide");
            this.toggleClassBody("show");
            $(".body_product_slider .page-main").css("height",(parseFloat(this.getSliderHeight()) + 100).toString() + "px" );
        },
        removeSliderCategoryStatus:function(){
            $(".body_product_slider .page-main").removeAttr("style");
            this.toggleCategoryTitle("show");
            this.toggleClassBody("hide");
        },
        toggleCategoryTitle: function(command){
            if(command=="hide"){
                $(".page-title-wrapper").hide();
            }
            if(command=="show"){
                $(".page-title-wrapper").show();
            }
        },
        toggleClassBody: function(command){
            if(command=="hide"){
                $("body").removeClass("body_product_slider");
            }
            if(command=="show"){
                if(!$("body").hasClass("body_product_slider")){
                    $("body").addClass("body_product_slider");
                }
            }
        },
        createSliderTemplate: function(){
            $(".product-slider .swiper-wrapper .swiper-slide").remove();
            this.getProductItemObjectList().each(function(index){
                var progressTmpl = mageTemplate('#swiper-template');
                var url = $(this).find(".product-item-photo").attr("href");
                var tmpl = progressTmpl({
                    url: url
                });
                $('#swiper-template').before(tmpl);
            });
        }
    };
});
