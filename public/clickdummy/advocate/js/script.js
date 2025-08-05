$(document).ready(function() {

    var selectorStrings = {
        uploadImageToggler: "#pill-upload-tab",
        uploadImageInput: "#uploadCovetImageInput",
        uploadImageInputValue: "#uploadCovetImageInputValue",
        editProfileImageToggler: "#editProfileImageToggler",
        editProfileImageInput: "#editProfileImageInput",
        editProfileImageValue: "#editProfileImageValue",
        editProfileImagePreview: "#editProfileImagePreview",
        covetTaggingThumbnailContainer: 'section__covet-result__thumbnail',
        covetTaggingTooltip: 'section__covet-result__tooltip',
        tooltipFormContainer: 'section__covet-result__tooltip-form',
        tooltipFormContainerArrow: 'section__covet-result__tooltip-form-arrow',
        tooltipCloseAction: 'close__tooltip-form'
    }
    
    $(selectorStrings.uploadImageToggler).on('click', function() {
        $(selectorStrings.uploadImageInput).click();
    });
    
    $(selectorStrings.uploadImageInput).on('change', function(e) {
        $("#uploadCovetImageInputValue").val(e.target.files[0].name);
    });
    
    $(selectorStrings.editProfileImageToggler).on('click', function() {
        $(selectorStrings.editProfileImageInput).click();
    });
    
    $(selectorStrings.editProfileImageInput).on('change', function(e) {
        readURL(this);
    });

    function readURL(input) {
        if (input.files && input.files[0]) {
          var reader = new FileReader();
          reader.onload = function(e) {
              $(selectorStrings.editProfileImageToggler).find('i, p').hide();
              $(selectorStrings.editProfileImagePreview).removeClass('d-none').attr('src', e.target.result);          
            }
          reader.readAsDataURL(input.files[0]);
        }
      }


      var tooltipSpan = document.getElementById(selectorStrings.covetTaggingTooltip),
      tooltipContainer = document.getElementById(selectorStrings.covetTaggingThumbnailContainer),
      TooltipFormContainer = document.getElementById(selectorStrings.tooltipFormContainer),
      TooltipFormContainerArrow = document.getElementById(selectorStrings.tooltipFormContainerArrow),
      TooltipCloseAction = document.getElementById(selectorStrings.tooltipCloseAction);

        tooltipContainer.onmousemove = function (e) {
            var x = e.clientX,
                y = e.clientY,
                container = tooltipContainer.getBoundingClientRect();

            tooltipSpan.style.top = (y - container.top + 5) + 'px';
            tooltipSpan.style.left = (x - container.left + 5) + 'px';
        };


        tooltipContainer.onclick = function(e) {
            var clickX = e.clientX,
                clickY = e.clientY,
                containerClick = tooltipContainer.getBoundingClientRect();
                formClick = TooltipFormContainer.getBoundingClientRect();

            TooltipFormContainer.style.opacity = '1';
            TooltipFormContainer.style.zIndex = '2';
            TooltipFormContainer.style.top = (clickY - containerClick.top + 15) + 'px';
            TooltipFormContainerArrow.style.left = (clickX - formClick.left - 15) + 'px';
        }


        TooltipCloseAction.onclick = function() {
            TooltipFormContainer.style.opacity = '0';
            TooltipFormContainer.style.zIndex = '-1';
        }
});


// Custom Dropdown of advocate page
// Inspiration: https://tympanus.net/codrops/2012/10/04/custom-drop-down-list-styling/

function DropDown(el) {
    this.dd = el;
    this.placeholder = this.dd.children('span');
    this.opts = this.dd.find('ul.drop li');
    this.val = '';
    this.index = -1;
    this.initEvents();
}

DropDown.prototype = {
    initEvents: function () {
        var obj = this;
        obj.dd.on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).toggleClass('active');
        });
        obj.opts.on('click', function () {
            var opt = $(this);
            obj.val = opt.text();
            obj.index = opt.index();
            obj.placeholder.text(obj.val);
            opt.siblings().removeClass('selected');
            opt.filter(':contains("' + obj.val + '")').addClass('selected');
        }).change();
    },
    getValue: function () {
        return this.val;
    },
    getIndex: function () {
        return this.index;
    }
};

$(function () {
    // create new variable for each menu
    var dd1 = new DropDown($('#advocate-status'));
    // var dd2 = new DropDown($('#other-gases'));
    $(document).click(function () {
        // close menu on document click
        $('.wrap-drop').removeClass('active');
    });
});
