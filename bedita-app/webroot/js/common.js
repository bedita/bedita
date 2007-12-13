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