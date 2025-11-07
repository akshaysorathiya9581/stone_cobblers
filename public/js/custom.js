$(document).ready(function () {

    // Initialize select2
    if ($.fn.select2) {
        $(".custom-select").each(function () {
            let placeholderText = $(this).attr("data-placeholder") || "Select option";
            $(this).select2({
                placeholder: placeholderText,
                allowClear: true,
                width: 'resolve'
            });
        });
    } else {
        console.warn("Select2 plugin not found — make sure it's loaded before custom.js");
    }

    // Sidebar toggle logic
    function getWidth() {
        return $(window).width();
    }

    $('.sidebar-toggle').on('click', function () {
        const width = getWidth();

        if (width >= 1199 && width <= 1549) {
            $('.sidebar').toggleClass('collapsed');
        } else if (width < 1199) {
            $('.sidebar').toggleClass('mobile-open');
        }
    });

    // Optional cleanup on resize
    $(window).on('resize', function () {
        const width = getWidth();
        if (width > 1549) {
            $('.sidebar').removeClass('collapsed mobile-open');
        }
    });
});