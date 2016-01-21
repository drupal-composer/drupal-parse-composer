core = 7.x
api = 2

projects[drupal][type] = "core"
projects[drupal][download][type] = "git"
projects[drupal][download][url] = "git://github.com/pantheon-systems/drops-7.git"
projects[drupal][download][branch] = "master"

projects[kw_manifests][type] = "module"
projects[kw_manifests][download][type] = "git"
projects[kw_manifests][download][url] = "git://github.com/kraftwagen/kw-manifests.git"
projects[kw_manifests][download][branch] = "master"
projects[kw_manifests][subdir] = "kraftwagen"

projects[kw_itemnames][type] = "module"
projects[kw_itemnames][download][type] = "git"
projects[kw_itemnames][download][url] = "git://github.com/kraftwagen/kw-itemnames.git"
projects[kw_itemnames][subdir] = "kraftwagen"

; The Panopoly Foundation

projects[panopoly_theme][type] = module
projects[panopoly_theme][subdir] = panopoly
projects[panopoly_theme][download][type] = git
projects[panopoly_theme][download][url] = http://git.drupal.org/project/panopoly_theme.git
projects[panopoly_theme][download][tag] = 7.x-1.0-rc5

projects[panopoly_magic][type] = module
projects[panopoly_magic][subdir] = panopoly
projects[panopoly_magic][download][type] = git
projects[panopoly_magic][download][url] = http://git.drupal.org/project/panopoly_magic.git
projects[panopoly_magic][download][tag] = 7.x-1.0-rc5

; The Panopoly Toolset

; The Panopoly Kalatheme
projects[kalatheme][version] = 1.2
projects[kalatheme][subdir] = contrib
projects[kalatheme][type] = "theme"
projects[kalatheme][download][type] = "get"
projects[kalatheme][download][url] = "https://ftp.drupal.org/files/projects/kalatheme-7.x-1.2.tar.gz"

libraries[jquery.cycle][download][type] = file
libraries[jquery.cycle][download][url] = http://raw.github.com/malsup/cycle/master/jquery.cycle.all.js

libraries[json2][download][type] = file
libraries[json2][download][url] = http://raw.github.com/douglascrockford/JSON-js/master/json2.js

libraries[bootstrap][download][type] = get
libraries[bootstrap][download][url] = http://getbootstrap.com/2.3.2/assets/bootstrap.zip

libraries[jquery.imagesloaded][download][type] = get
libraries[jquery.imagesloaded][download][url] = https://github.com/desandro/imagesloaded/archive/v2.1.2.tar.gz

libraries[jquery.imgareaselect][download][type] = get
libraries[jquery.imgareaselect][download][url] = http://odyniec.net/projects/imgareaselect/jquery.imgareaselect-0.9.10.zip

libraries[php-opencloud][download][type] = get
libraries[php-opencloud][download][url] = https://github.com/rackspace/php-opencloud/archive/v1.5.10.zip

libraries[flexslider][download][type] = get
libraries[flexslider][download][url] = http://github.com/woothemes/FlexSlider/archive/version/2.2.1.zip
libraries[flexslider][directory_name] = "flexslider"
libraries[flexslider][destination] = "libraries"

; Contrib Themes
; --------
projects[omega][version] = 4.1
projects[omega][type] = "theme"
projects[omega][subdir] = contrib
projects[omega][download][type] = git
projects[omega][download][url] = http://git.drupal.org/project/omega.git
projects[omega][download][tag] = 7.x-4.1

; Contrib modules
; --------
projects[total_control][type] = module
projects[total_control][subdir] = contrib
projects[total_control][download][type] = git
projects[total_control][download][url] = http://git.drupal.org/project/total_control.git
projects[total_control][download][tag] = 7.x-2.4
projects[total_control][patch][] = https://gist.github.com/MrMaksimize/9d22474c55b9616b06c9/raw/882dcd18f9b1c1f611c02b7286ee4803e2bbf84c/disable_dashboard.patch

projects[role_export][type] = module
projects[role_export][subdir] = contrib
projects[role_export][download][type] = git
projects[role_export][download][url] = http://git.drupal.org/project/role_export.git
projects[role_export][download][tag] = 7.x-1.0

projects[simplify][type] = module
projects[simplify][subdir] = contrib
projects[simplify][download][type] = git
projects[simplify][download][url] = http://git.drupal.org/project/simplify.git
projects[simplify][download][tag] = 7.x-3.1

projects[flexslider][type] = module
projects[flexslider][subdir] = contrib
projects[flexslider][download][type] = git
projects[flexslider][download][url] = http://git.drupal.org/project/flexslider.git
projects[flexslider][download][branch] = 7.x-2.x

projects[special_menu_items][type] = module
projects[special_menu_items][subdir] = contrib
projects[special_menu_items][download][type] = git
projects[special_menu_items][download][url] = http://git.drupal.org/project/special_menu_items.git
projects[special_menu_items][download][branch] = 7.x-2.x

projects[entityform][type] = module
projects[entityform][subdir] = contrib
projects[entityform][download][type] = git
projects[entityform][download][url] = http://git.drupal.org/project/entityform.git
projects[entityform][download][tag] = 7.x-1.2

projects[rules][type] = module
projects[rules][subdir] = contrib
projects[rules][download][type] = git
projects[rules][download][url] = http://git.drupal.org/project/rules.git
projects[rules][download][tag] = 7.x-2.6

projects[sharethis][type] = module
projects[sharethis][subdir] = contrib
projects[sharethis][download][type] = git
projects[sharethis][download][url] = http://git.drupal.org/project/sharethis.git
projects[sharethis][download][branch] = 7.x-2.x

projects[views_data_export][type] = module
projects[views_data_export][subdir] = contrib
projects[views_data_export][download][type] = git
projects[views_data_export][download][url] = http://git.drupal.org/project/views_data_export.git
projects[views_data_export][download][tag] = 7.x-3.0-beta7

projects[views_field_view][type] = module
projects[views_field_view][subdir] = contrib
projects[views_field_view][download][type] = git
projects[views_field_view][download][url] = http://git.drupal.org/project/views_field_view.git
projects[views_field_view][download][tag] = 7.x-1.1

projects[rate][type] = module
projects[rate][subdir] = contrib
projects[rate][download][type] = git
projects[rate][download][url] = http://git.drupal.org/project/rate.git
projects[rate][download][tag] = 7.x-1.6

projects[votingapi][type] = module
projects[votingapi][subdir] = contrib
projects[votingapi][download][type] = git
projects[votingapi][download][url] = http://git.drupal.org/project/votingapi.git
projects[votingapi][download][tag] = 7.x-2.11

projects[views_slideshow][type] = module
projects[views_slideshow][subdir] = contrib
projects[views_slideshow][download][type] = git
projects[views_slideshow][download][url] = http://git.drupal.org/project/views_slideshow.git
projects[views_slideshow][download][tag] = 7.x-3.1

projects[addressfield][type] = module
projects[addressfield][subdir] = contrib
projects[addressfield][download][type] = git
projects[addressfield][download][url] = http://git.drupal.org/project/addressfield.git
projects[addressfield][download][tag] = 7.x-1.0-beta4

projects[features_orphans][type] = module
projects[features_orphans][subdir] = contrib
projects[features_orphans][download][type] = git
projects[features_orphans][download][url] = http://git.drupal.org/project/features_orphans.git
projects[features_orphans][download][tag] = 7.x-1.2

projects[diff][type] = module
projects[diff][subdir] = contrib
projects[diff][download][type] = git
projects[diff][download][url] = http://git.drupal.org/project/diff.git
projects[diff][download][tag] = 7.x-3.2

projects[radioactivity][type] = module
projects[radioactivity][subdir] = contrib
projects[radioactivity][download][type] = git
projects[radioactivity][download][url] = http://git.drupal.org/project/radioactivity.git
projects[radioactivity][download][tag] = 7.x-2.8

projects[phone][type] = module
projects[phone][subdir] = contrib
projects[phone][download][type] = git
projects[phone][download][url] = http://git.drupal.org/project/phone.git
projects[phone][download][branch] = 7.x-1.x

projects[search_api][type] = module
projects[search_api][subdir] = contrib
projects[search_api][download][type] = git
projects[search_api][download][url] = http://git.drupal.org/project/search_api.git
projects[search_api][download][tag] = 7.x-1.9
projects[search_api][patch][2139215] = https://drupal.org/files/issues/2139215-1--batch_callback_context.patch
projects[search_api][patch][2110315] = https://drupal.org/files/2110315-18--views_taxonomy_term_filter.patch

projects[search_api_db][type] = module
projects[search_api_db][subdir] = contrib
projects[search_api_db][download][type] = git
projects[search_api_db][download][url] = http://git.drupal.org/project/search_api_db.git
projects[search_api_db][download][tag] = 7.x-1.0

projects[facetapi][type] = module
projects[facetapi][subdir] = contrib
projects[facetapi][download][type] = git
projects[facetapi][download][url] = http://git.drupal.org/project/facetapi.git
projects[facetapi][download][tag] = 7.x-1.3

projects[oauth][type] = module
projects[oauth][subdir] = contrib
projects[oauth][download][type] = git
projects[oauth][download][url] = http://git.drupal.org/project/oauth.git
projects[oauth][download][tag] = 7.x-3.1

projects[inline_entity_form][type] = module
projects[inline_entity_form][subdir] = contrib
projects[inline_entity_form][download][type] = git
projects[inline_entity_form][download][url] = http://git.drupal.org/project/inline_entity_form.git
projects[inline_entity_form][download][tag] = 7.x-1.3

projects[redirect][type] = module
projects[redirect][subdir] = contrib
projects[redirect][download][type] = git
projects[redirect][download][url] = http://git.drupal.org/project/redirect.git
projects[redirect][download][tag] = 7.x-1.0-rc1

projects[simpleads][type] = module
projects[simpleads][subdir] = contrib
projects[simpleads][download][type] = git
projects[simpleads][download][url] = http://git.drupal.org/project/simpleads.git
projects[simpleads][download][tag] = 7.x-1.7
projects[simpleads][patch][] = https://gist.github.com/dsdeiz/39ff1456f080e6696129/raw/d6c5731f93d3fafe3365c50eaa6208378220386a/simpleads.patch

projects[message][type] = module
projects[message][subdir] = contrib
projects[message][download][type] = git
projects[message][download][url] = http://git.drupal.org/project/message.git
projects[message][download][tag] = 7.x-1.9

projects[message_notify][type] = module
projects[message_notify][subdir] = contrib
projects[message_notify][download][type] = git
projects[message_notify][download][url] = http://git.drupal.org/project/message_notify.git
projects[message_notify][download][tag] = 7.x-2.5

projects[nodequeue][type] = module
projects[nodequeue][subdir] = contrib
projects[nodequeue][download][type] = git
projects[nodequeue][download][url] = http://git.drupal.org/project/nodequeue.git
projects[nodequeue][download][tag] = 7.x-2.0-beta1

projects[features_extra][type] = module
projects[features_extra][subdir] = contrib
projects[features_extra][download][type] = git
projects[features_extra][download][url] = http://git.drupal.org/project/features_extra.git
projects[features_extra][download][tag] = 7.x-1.0-beta1

projects[workbench][type] = module
projects[workbench][subdir] = contrib
projects[workbench][download][type] = git
projects[workbench][download][url] = http://git.drupal.org/project/workbench.git
projects[workbench][download][tag] = 7.x-1.2

projects[workbench_moderation][type] = module
projects[workbench_moderation][subdir] = contrib
projects[workbench_moderation][download][type] = git
projects[workbench_moderation][download][url] = http://git.drupal.org/project/workbench_moderation.git
projects[workbench_moderation][download][tag] = 7.x-1.3

projects[session_limit][type] = module
projects[session_limit][subdir] = contrib
projects[session_limit][download][type] = git
projects[session_limit][download][url] = http://git.drupal.org/project/session_limit.git
projects[session_limit][download][tag] = 7.x-2.0-rc2

projects[ejectorseat][type] = module
projects[ejectorseat][subdir] = contrib
projects[ejectorseat][download][type] = git
projects[ejectorseat][download][url] = http://git.drupal.org/project/ejectorseat.git
projects[ejectorseat][download][tag] = 7.x-1.0

projects[manualcrop][type] = module
projects[manualcrop][subdir] = contrib
projects[manualcrop][download][type] = git
projects[manualcrop][download][url] = http://git.drupal.org/project/manualcrop.git
projects[manualcrop][download][revision] = fb91616053beab18ea5d630d27c6ba

projects[node_view_permissions][type] = module
projects[node_view_permissions][subdir] = contrib
projects[node_view_permissions][download][type] = git
projects[node_view_permissions][download][url] = http://git.drupal.org/project/node_view_permissions.git
projects[node_view_permissions][download][tag] = 7.x-1.3

projects[scheduler][type] = module
projects[scheduler][subdir] = contrib
projects[scheduler][download][type] = git
projects[scheduler][download][url] = http://git.drupal.org/project/scheduler.git
projects[scheduler][download][tag] = 7.x-1.1

projects[scheduler_workbench][type] = module
projects[scheduler_workbench][subdir] = contrib
projects[scheduler_workbench][download][type] = git
projects[scheduler_workbench][download][url] = http://git.drupal.org/project/scheduler_workbench.git
projects[scheduler_workbench][download][tag] = 7.x-1.2

projects[cdn][type] = module
projects[cdn][subdir] = contrib
projects[cdn][download][type] = git
projects[cdn][download][url] = http://git.drupal.org/project/cdn.git
projects[cdn][download][tag] = 7.x-2.6

projects[cloud_files][type] = module
projects[cloud_files][subdir] = contrib
projects[cloud_files][download][type] = git
projects[cloud_files][download][url] = http://git.drupal.org/project/cloud_files.git
projects[cloud_files][download][tag] = 7.x-1.1
projects[cloud_files][patch][] = https://gist.githubusercontent.com/dsdeiz/88fcde72b662209ac2af/raw/84643ac9fbfe2df3c8079f33ebf4544a3be9fbce/cloud_files.patch

projects[weather][type] = module
projects[weather][subdir] = contrib
projects[weather][download][type] = git
projects[weather][download][url] = http://git.drupal.org/project/weather.git
projects[weather][download][branch] = 7.x-2.4

projects[migrate][type] = module
projects[migrate][subdir] = contrib
projects[migrate][download][type] = git
projects[migrate][download][url] = http://git.drupal.org/project/migrate.git
projects[migrate][download][tag] = 7.x-2.5

projects[menu_attributes][type] = module
projects[menu_attributes][subdir] = contrib
projects[menu_attributes][download][type] = git
projects[menu_attributes][download][url] = http://git.drupal.org/project/menu_attributes.git
projects[menu_attributes][download][branch] = 7.x-1.x

projects[composer_manager][type] = module
projects[composer_manager][subdir] = contrib

projects[custom_breadcrumbs][type] = module
projects[custom_breadcrumbs][subdir] = contrib
projects[custom_breadcrumbs][download][type] = git
projects[custom_breadcrumbs][download][url] = http://git.drupal.org/project/custom_breadcrumbs.git
projects[custom_breadcrumbs][download][tag] = 7.x-2.0-alpha3

projects[google_analytics][type] = module
projects[google_analytics][subdir] = contrib
projects[google_analytics][download][type] = git
projects[google_analytics][download][url] = http://git.drupal.org/project/google_analytics.git
projects[google_analytics][download][tag] = 7.x-1.4

projects[metatag][type] = module
projects[metatag][subdir] = contrib
projects[metatag][download][type] = git
projects[metatag][download][url] = http://git.drupal.org/project/metatag.git
projects[metatag][download][tag] = 7.x-1.0-beta9

projects[token_filter][type] = module
projects[token_filter][subdir] = contrib
projects[token_filter][download][type] = git
projects[token_filter][download][url] = http://git.drupal.org/project/token_filter.git
projects[token_filter][download][branch] = 7.x-1.x

projects[token_insert][type] = module
projects[token_insert][subdir] = contrib
projects[token_insert][download][type] = git
projects[token_insert][download][url] = http://git.drupal.org/project/token_insert.git
projects[token_insert][download][tag] = 7.x-2.1

projects[linkit_target][type] = module
projects[linkit_target][subdir] = contrib
projects[linkit_target][download][type] = git
projects[linkit_target][download][url] = http://git.drupal.org/project/linkit_target.git
projects[linkit_target][download][tag] = 7.x-1.0

projects[xmlsitemap][type] = module
projects[xmlsitemap][subdir] = contrib
projects[xmlsitemap][download][type] = git
projects[xmlsitemap][download][url] = http://git.drupal.org/project/xmlsitemap.git
projects[xmlsitemap][download][tag] = 7.x-2.0-rc2

projects[googlenews][type] = module
projects[googlenews][subdir] = contrib
projects[googlenews][download][type] = git
projects[googlenews][download][url] = http://git.drupal.org/project/googlenews.git
projects[googlenews][download][tag] = 7.x-1.6

; Overrides
includes[panopoly_admin_make] = panopoly_overrides/panopoly_admin.make
includes[panopoly_core_make] = panopoly_overrides/panopoly_core.make
includes[panopoly_search_make] = panopoly_overrides/panopoly_search.make
includes[panopoly_wysiwyg_make] = panopoly_overrides/panopoly_wysiwyg.make
includes[panopoly_widgets_make] = panopoly_overrides/panopoly_widgets.make
