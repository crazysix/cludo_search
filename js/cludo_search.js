var CludoSearch;

(function ($, Drupal, drupalSettings) {
Drupal.behaviors.CludoSearchBehavior = {
  attach: function (context, settings) {
    // can access setting from 'drupalSettings';
    var cludoSettings = {
        customerId: drupalSettings.cludo_search.cludo_searchJS.customerId,
        engineId: drupalSettings.cludo_search.cludo_searchJS.engineId,
        searchUrl: drupalSettings.cludo_search.cludo_searchJS.searchUrl,
        disableAutocomplete: drupalSettings.cludo_search.cludo_searchJS.disableAutocomplete,
        hideResultsCount: drupalSettings.cludo_search.cludo_searchJS.hideResultsCount,
        hideSearchDidYouMean: drupalSettings.cludo_search.cludo_searchJS.hideSearchDidYouMean,
        hideSearchFilters: drupalSettings.cludo_search.cludo_searchJS.hideSearchFilters,
        language: 'en',
        searchInputs: ["cludo-search-block-form","cludo-search-search-form"],
        type: 'inline',
        filters: drupalSettings.cludo_search.cludo_searchJS.filters,
        customCallbackAfterSearch: cludoSearchRestrictFilters,
        initFacets: null//{"Category": [""]}
      };
    CludoSearch = new Cludo(cludoSettings);
    CludoSearch.init();

    // Add whitelist values to the cludo object so we can use them later.
    CludoSearch.whitelistCategories = drupalSettings.cludo_search.cludo_searchJS.whitelistFilters;

  }
};
})(jQuery, Drupal, drupalSettings);

// Remove unwanted filters.
function cludoSearchRestrictFilters() {
  if (!CludoSearch.hideSearchFilters) {
    // White listed facets.
    var whitelistFacets = CludoSearch.whitelistCategories;

    // Only proceed if a whitelist exists.
    if (whitelistFacets.length > 1) {

      if (CludoSearch.elemSearchResults.filters !== null) {
        // Get current facets if any.
        if (CludoSearch.facets.Category.length > 0) {
          // Also whitelist current facets.
          whitelistFacets = jQuery.merge(whitelistFacets, CludoSearch.facets.Category);
        }

        for (var j = 0, l = CludoSearch.elemSearchResults.filters.length; j < l; j++) {
          var filters = CludoSearch.elemSearchResults.filters[j].querySelectorAll('LI');
          for (var i = 0, len = filters.length; i < len; i++) {
            var selector = filters[i].querySelector('a');
            if (selector != null) {
              var facetValue = selector.getAttribute("data-facet");
              if (jQuery.inArray(facetValue, whitelistFacets) < 0 && facetValue != "null") {
                filters[i].remove();
              }
            }
          }
        }
      }
    }
  }
}
