# Senkei Framework (Empty) v 1.0.0

Dossier contenant une architecture "vide" pour me démarrage d'un nouveau projet


### Vue d'ensemble

```
senkei_framework/
  |
  |- app/
  |  |- controllers/
  |  |  |- ajax_controllers/
  |  |  |  |- site/
  |  |  |  |  |- <AjaxSiteController>
  |  |  |  |  |
  |  |  |- simple_controllers/
  |  |  |  |- site/
  |  |  |  |  |- <indexController>
  |  |  |  |  |
  |  |  |- special_controllers/
  |  |  |  |- <Error404Controller>
  |  |  |
  |  |- models/
  |  |  |
  |  |- services/
  |  |  |- <Hash>
  |  |  |- <Paginate>
  |  |  |- <SendMail>
  |  |  |- <Session>
  |  |  |- <UploadFile>
  |  |  |- <Validator>
  |  |  |
  |  |- vendors/
  |  |  |- less/
  |  |  |- twig/
  |  |  |- yaml/
  |  |  |
  |  |
  |
  |- configuration/
  |  |- admin/
  |  |  |- <pages>
  |  |  |
  |  |- database/
  |  |  |- <config_db>
  |  |  |
  |  |- design files/
  |  |  |- <config_bootstrap>
  |  |  |- <config_knacss>
  |  |  |- <config_jsfile>
  |  |  |- <config_less>
  |  |  |- <config_stylesheet>
  |  |  |
  |  |- environnement/
  |  |  |- <config_environment>
  |  |  |
  |  |- routes/
  |  |  |- <config_routes>
  |  |  |
  |  |- twig/
  |  |  |- <config_templates>
  |  |  |- <config_twig>
  |  |
  |
  |- core/
  |  |- database/
  |  |  |- <Database>
  |  |  |- <ModelsProvider>
  |  |  |
  |  |- system/
  |  |  |- <ControllersProviderSystem>
  |  |  |- <RoutageSystem>
  |  |  |- <System>
  |  |  |
  |  |- system_services/
  |  |  |- <AdminSystem>
  |  |  |- <EnvironmentSystem>
  |  |  |- <FilesDependancySystem>
  |  |  |- <LangSystem>
  |  |  |- <TwigSystem>
  |  |  |
  |  |- <Autoloader>
  |  |- <Configuration>
  |  |
  |
  |- lang/
  |  |- <en>
  |  |- <fr>
  |
  |- templates/
  |  |- mail_tempaltes/
  |  |  |- <mail_confirmation_inscription>
  |  |
  |
  |- views/
  |  |- macros/
  |  |  |- <bootstrapForm>
  |  |  |- <bootstrapNotif>
  |  |  |- <paginate>
  |  |  |
  |  |- pages/
  |  |  |- site/
  |  |  |  |- <index.twig>
  |  |  |
  |  |- <layout.twig>
  |  |- <layout_site.twig>
  |
  |- www/
  |  |- ajax/
  |  |  |- <index ajax>
  |  |- assets/
  |  |  |- bootstrap/
  |  |  |- css/
  |  |  |- fonts/
  |  |  |- js/
  |  |  |- less/
  |  |  |- pictures/
  |  |
  |
  |- index
```

=======
