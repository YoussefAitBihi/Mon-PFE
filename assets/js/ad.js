$(function () {

   var counter = 0;
   // Add Sous-Form
   $('#add-image').on('click', function() {

      const tmpl  = $('#ad_images').data('prototype').replace(/__name__/g, counter);

      // Append form to ad_images
      $('#ad_images').append(tmpl);

      counter+=1;

      // Delete Sous-Form
      deleteForm();

   });

   // Delete Sous-Form
   function deleteForm() {
      $('button[data-action="delete"]').on('click', function(){
         $(this.dataset.target).remove();
      });
   }

   deleteForm();

});