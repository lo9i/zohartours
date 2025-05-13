/*!
 * remark (http://getbootstrapadmin.com/remark)
 * Copyright 2015 amazingsurge
 * Licensed under the Themeforest Standard Licenses
 */
$.components.register("summernote", {
  mode: "default",
  defaults: {
    toolbar: [
      // [groupName, [list of button]]
      ['style', ['bold', 'italic', 'underline']],
      ['font', ['strikethrough', 'superscript', 'subscript']],
      ['fontsize', ['fontsize']],
      ['para', ['ul', 'ol', 'paragraph']],
    ],
    height: 500
  }
});
