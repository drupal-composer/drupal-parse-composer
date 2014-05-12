; Panopoly Search Makefile

api = 2
core = 7.x

; Search API and Facet API Modules

projects[search_api_solr][version] = 1.3
projects[search_api_solr][subdir] = contrib

; Solr PHP Client Library

libraries[SolrPhpClient][download][type] = get
libraries[SolrPhpClient][download][url] = http://solr-php-client.googlecode.com/files/SolrPhpClient.r60.2011-05-04.zip
