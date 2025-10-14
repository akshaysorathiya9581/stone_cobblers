$(".custom-select").each(function () {
    let placeholderText = $(this).attr("data-placeholder") || "Select option";
    $(this).select2({
        placeholder: placeholderText,
        allowClear: true
    });
});