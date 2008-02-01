// Customize two date pickers to work as a date range 
function customRange(input) { 
    return {minDate: (input.id == 'end' ? $('#start').getDatepickerDate() : null), 
        	maxDate: (input.id == 'start' ? $('#end').getDatepickerDate() : null)}; 
}

jQuery.fn.extend({
  check: function() {
     return this.each(function() { this.checked = true; });
   },
  uncheck: function() {
     return this.each(function() { this.checked = false; });
   },
  toggleCheck: function() {
     return this.each(function() { this.checked = !this.checked ; });
   }
});