# Senkei Framework v 1.0.0

Framework intégralement  développé from scratch, respectant les concepts de Model View Controller, et de Programmation Orientée Objet.

### Lien demo

http://vacherot.etudiant-eemi.com/perso/dossier/malcolm0810/private/s2705/senkei_framework/

### Vue d'ensemble

```
senkei_framework/
  |
  |- app/
  |  |- controllers/
  |  |  |- ajax_controllers/
  |  |  |  |- dossier_module_ajax1/
  |  |  |  |  |- <AjaxControllers>
  |  |  |  |  |
  |  |  |- simple_controllers/
  |  |  |  |- dossier_module1/
  |  |  |  |  |- <Controllers>
  |  |  |  |  |
  |  |  |  |- dossier_module2/
  |  |  |  |  |- <Controllers>
  |  |  |  |  |
  |  |  |- special_controllers/
  |  |  |  |- <specialControllers>
  |  |  |
  |  |- models/
  |  |  |- <models de données>
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
  |  |  |- dossier_module/
  |  |  |  |- <pages>
  |  |  |- dossier_module2/
  |  |  |  |- <pages>
  |  |  |
  |  |- <fichiers layout>
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
