<?php

namespace tests\AppBundle\Controller;





use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class AdminControllerTest extends WebTestCase
{

    public function testSiUnVisiteurEffectueUneRequeteSurUrlAdminSansEtreConnecter()
    {
        $client = static::createClient();

        $client->request('GET', '/admin');

        $crawler = $client->followRedirect();

        $this->assertEquals(1, $crawler->filter('h2:contains("Identification")')->count());
    }

    public function testSiUnVisiteurEffectueUneRequeteSurUrlAdminEnEtantConnecter()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $linkConnexion = $crawler->filter('a:contains("Connexion")');

        $crawler = $client->click($linkConnexion->link());

        $client->followRedirects();

        $this->assertEquals(1, $crawler->filter('h2:contains("Identification")')->count());

        $form = $crawler->selectButton('_submit')->form();

        $form['_username'] = 'admin';
        $form['_password'] = 'admin';

        $crawler = $client->submit($form);

        $this->assertEquals(1, $crawler->filter('a:contains("admin ")')->count());


        $crawler = $client->request('GET', '/admin');

        $this->assertEquals(1, $crawler->filter('h1:contains("Tableau de bord")')->count());
    }

    public function testLienUtilisateursDansAdministration()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $linkConnexion = $crawler->filter('a:contains("Connexion")');

        $crawler = $client->click($linkConnexion->link());

        $client->followRedirects();

        $form = $crawler->selectButton('_submit')->form();

        $form['_username'] = 'admin';
        $form['_password'] = 'admin';

        $client->submit($form);

        $crawler = $client->request('GET', '/admin');

        $linkUtilisateur = $crawler->filter('a:contains("Utilisateurs")');

        $crawler = $client->click($linkUtilisateur->link());

        $this->assertEquals(1, $crawler->filter('h1:contains("Gestion utilisateurs")')->count());
    }

    public function testLienConfigurationDansAdministration()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $linkConnexion = $crawler->filter('a:contains("Connexion")');

        $crawler = $client->click($linkConnexion->link());

        $client->followRedirects();

        $form = $crawler->selectButton('_submit')->form();

        $form['_username'] = 'admin';
        $form['_password'] = 'admin';

        $client->submit($form);

        $crawler = $client->request('GET', '/admin');

        $linkConfiguration = $crawler->filter('a:contains("Configuration")');

        $crawler = $client->click($linkConfiguration->link());

        $this->assertEquals(1, $crawler->filter('h1:contains("Configuration du site")')->count());
    }

    public function testLienEspeceDansAdministration()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $linkConnexion = $crawler->filter('a:contains("Connexion")');

        $crawler = $client->click($linkConnexion->link());

        $client->followRedirects();

        $form = $crawler->selectButton('_submit')->form();

        $form['_username'] = 'admin';
        $form['_password'] = 'admin';

        $client->submit($form);

        $crawler = $client->request('GET', '/admin');

        $linkEspece = $crawler->filter('a:contains("Les espèces")');

        $crawler = $client->click($linkEspece->link());

        $this->assertEquals(1, $crawler->filter('h1:contains("Affichage de toutes les espèces.")')->count());
    }

    public function testLienObservationsDansAdministration()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $linkConnexion = $crawler->filter('a:contains("Connexion")');

        $crawler = $client->click($linkConnexion->link());

        $client->followRedirects();

        $form = $crawler->selectButton('_submit')->form();

        $form['_username'] = 'admin';
        $form['_password'] = 'admin';

        $client->submit($form);

        $crawler = $client->request('GET', '/admin');

        $linkObservation = $crawler->filter('a:contains("Observations")');

        $crawler = $client->click($linkObservation->link());

        $this->assertEquals(1, $crawler->filter('h1:contains("Affichage de toutes les observations.")')->count());
    }

    public function testLienNewsletterDansAdministration()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $linkConnexion = $crawler->filter('a:contains("Connexion")');

        $crawler = $client->click($linkConnexion->link());

        $client->followRedirects();

        $form = $crawler->selectButton('_submit')->form();

        $form['_username'] = 'admin';
        $form['_password'] = 'admin';

        $client->submit($form);

        $crawler = $client->request('GET', '/admin');

        $linkNewsletter = $crawler->filter('a:contains("Newsletter")');

        $crawler = $client->click($linkNewsletter->link());

        $this->assertEquals(1, $crawler->filter('h1:contains("Rédaction Newsletter")')->count());
    }

    public function testDesactiverUtilisateurJeannotDansAdministrationPuisEssayerDeSeReconnecter()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $linkConnexion = $crawler->filter('a:contains("Connexion")');

        $crawler = $client->click($linkConnexion->link());

        $client->followRedirects();

        $form = $crawler->selectButton('_submit')->form();

        $form['_username'] = 'admin';
        $form['_password'] = 'admin';

        $client->submit($form);

        $crawler = $client->request('GET', '/admin');

        $linkUtilisateur = $crawler->filter('a:contains("Utilisateurs")');

        $crawler = $client->click($linkUtilisateur->link());

        $linkDesactiver = $crawler->filter('a.lienDisable')->eq('2');

        $crawler = $client->click($linkDesactiver->link());

        $linkDeconnexion = $crawler->filter('a:contains("Déconnexion")');

        $crawler = $client->click($linkDeconnexion->link());

        $linkReconnexion = $crawler->filter('a:contains("Connexion")');

        $crawler = $client->click($linkReconnexion->link());

        $client->followRedirects();

        $form = $crawler->selectButton('_submit')->form();

        $form['_username'] = 'Jeannot';
        $form['_password'] = 'jeannot';

        $crawler = $client->submit($form);

        $client->followRedirects();

        $this->assertEquals(1, $crawler->filter('p:contains("Le compte est désactivé.")')->count());

    }

    public function testReactiverUtilisateurJeannotDansAdministrationPuisEssayerDeSeReconnecter()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $linkConnexion = $crawler->filter('a:contains("Connexion")');

        $crawler = $client->click($linkConnexion->link());

        $client->followRedirects();

        $form = $crawler->selectButton('_submit')->form();

        $form['_username'] = 'admin';
        $form['_password'] = 'admin';

        $client->submit($form);

        $crawler = $client->request('GET', '/admin');

        $linkUtilisateur = $crawler->filter('a:contains("Utilisateurs")');

        $crawler = $client->click($linkUtilisateur->link());

        $linkActiver = $crawler->filter('a.lienEnable')->eq('0');

        $crawler = $client->click($linkActiver->link());

        $linkDeconnexion = $crawler->filter('a:contains("Déconnexion")');

        $crawler = $client->click($linkDeconnexion->link());

        $client->followRedirects();

        $linkReconnexion = $crawler->filter('a:contains("Connexion")');

        $crawler = $client->click($linkReconnexion->link());

        $client->followRedirects();

        $form = $crawler->selectButton('_submit')->form();

        $form['_username'] = 'Jeannot';
        $form['_password'] = 'jeannot';

        $crawler = $client->submit($form);

        $client->followRedirects();

        $this->assertEquals(1, $crawler->filter('a:contains("Jeannot")')->count());

    }

    public function testEditerUtilisateurJeannotDansAdministration()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $linkConnexion = $crawler->filter('a:contains("Connexion")');

        $crawler = $client->click($linkConnexion->link());

        $client->followRedirects();

        $form = $crawler->selectButton('_submit')->form();

        $form['_username'] = 'admin';
        $form['_password'] = 'admin';

        $client->submit($form);

        $client->followRedirects();

        $crawler = $client->request('GET', '/admin');

        $linkUtilisateur = $crawler->filter('a:contains("Utilisateurs")');

        $crawler = $client->click($linkUtilisateur->link());

        $linkEdition = $crawler->filter('a.editionUser')->eq('2');

        $crawler = $client->click($linkEdition->link());

        $client->followRedirects();

        $this->assertEquals(1, $crawler->filter('h1:contains("Edition utilisateur")')->count());

        $formEdit = $crawler->selectButton('Valider')->form();

        $formEdit['user[name]'] = 'Jean Voye';
        $formEdit['user[username]'] = 'Jeannott';

        $crawler = $client->submit($formEdit);

        $this->assertEquals(1, $crawler->filter('td:contains("Jeannott")')->count());

        $crawler = $client->click($linkEdition->link());

        $client->followRedirects();

        $this->assertEquals(1, $crawler->filter('h1:contains("Edition utilisateur")')->count());

        $formEdit = $crawler->selectButton('Valider')->form();

        // Retour Etat initial

        $formEdit['user[name]'] = 'Jean Voye';
        $formEdit['user[username]'] = 'Jeannot';

        $crawler = $client->submit($formEdit);

        $this->assertEquals(1, $crawler->filter('td:contains("Jeannot")')->count());

    }

    public function testChangerConfigurationAdministration()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $linkConnexion = $crawler->filter('a:contains("Connexion")');

        $crawler = $client->click($linkConnexion->link());

        $client->followRedirects();

        $form = $crawler->selectButton('_submit')->form();

        $form['_username'] = 'admin';
        $form['_password'] = 'admin';

        $client->submit($form);

        $crawler = $client->request('GET', '/admin');

        $linkConfiguration = $crawler->filter('a:contains("Configuration")');

        $crawler = $client->click($linkConfiguration->link());

        $formConfig = $crawler->selectButton('Valider')->form();

        $formConfig['appbundle_configuration[themeAdmin]'] = 'css/bootstrap-yeti/bootstrap.min.css';

        $crawler = $client->submit($formConfig);

        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());

        // Retour Etat Initial

        $formConfig = $crawler->selectButton('Valider')->form();

        $formConfig['appbundle_configuration[themeAdmin]'] = 'css/bootstrap-slate/bootstrap.min.css';

        $crawler = $client->submit($formConfig);

        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());


    }
    public function testChangerConfigurationFrontEnd()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $linkConnexion = $crawler->filter('a:contains("Connexion")');

        $crawler = $client->click($linkConnexion->link());

        $client->followRedirects();

        $form = $crawler->selectButton('_submit')->form();

        $form['_username'] = 'admin';
        $form['_password'] = 'admin';

        $client->submit($form);

        $crawler = $client->request('GET', '/admin');

        $linkConfiguration = $crawler->filter('a:contains("Configuration")');

        $crawler = $client->click($linkConfiguration->link());

        $formConfig = $crawler->selectButton('Valider')->form();

        $formConfig['appbundle_configuration[theme]'] = 'css/bootstrap-yeti/bootstrap.min.css';

        $crawler = $client->submit($formConfig);

        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());

        // Retour Etat Initial

        $formConfig = $crawler->selectButton('Valider')->form();

        $formConfig['appbundle_configuration[theme]'] = 'css/bootstrap-sandstone/bootstrap.min.css';

        $crawler = $client->submit($formConfig);

        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());


    }
    public function testVoirEtEditionEspece()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $linkConnexion = $crawler->filter('a:contains("Connexion")');

        $crawler = $client->click($linkConnexion->link());

        $client->followRedirects();

        $form = $crawler->selectButton('_submit')->form();

        $form['_username'] = 'admin';
        $form['_password'] = 'admin';

        $client->submit($form);

        $crawler = $client->request('GET', '/admin');

        $linkEspece = $crawler->filter('a:contains("Les espèces")');

        $crawler = $client->click($linkEspece->link());

        $linkVoir = $crawler->filter('a:contains("Voir")')->eq('0');

        $crawler = $client->click($linkVoir->link());

        $this->assertEquals(1, $crawler->filter('td:contains("Autorité (Auteur, année")')->count());

        $crawler = $client->click($linkEspece->link());

        $linkEditer = $crawler->filter('a:contains("Editer")')->eq('0');

        $crawler = $client->click($linkEditer->link());

        $this->assertEquals(1, $crawler->filter('h2:contains("Editer une fiche")')->count());

        $form = $crawler->selectButton('Valider')->form();

        $form['appbundle_taxrefv10[Famille]'] = 'test';

        $crawler = $client->submit($form);

        $this->assertEquals(1, $crawler->filter('td:contains("test")')->count());

        // Retour Etat initial

        $client->click($linkEspece->link());

        $crawler = $client->click($linkEditer->link());

        $this->assertEquals(1, $crawler->filter('h2:contains("Editer une fiche")')->count());

        $form = $crawler->selectButton('Valider')->form();

        $form['appbundle_taxrefv10[Famille]'] = '';

        $crawler = $client->submit($form);

        $this->assertNotEquals(1, $crawler->filter('td:contains("test")')->count());

    }
    public function testVoirEtEditionObservation()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $linkConnexion = $crawler->filter('a:contains("Connexion")');

        $crawler = $client->click($linkConnexion->link());

        $client->followRedirects();

        $form = $crawler->selectButton('_submit')->form();

        $form['_username'] = 'admin';
        $form['_password'] = 'admin';

        $client->submit($form);

        $crawler = $client->request('GET', '/admin');

        $linkObservations = $crawler->filter('a:contains("Observations")');

        $crawler = $client->click($linkObservations->link());

        $linkVoir = $crawler->filter('a:contains("Voir")')->eq('0');

        $crawler = $client->click($linkVoir->link());

        $this->assertEquals(1, $crawler->filter('td:contains("Scolopacidae")')->count());

        $crawler = $client->click($linkObservations->link());

        $linkEditer = $crawler->filter('a:contains("Editer")')->eq('0');

        $crawler = $client->click($linkEditer->link());

        $this->assertEquals(1, $crawler->filter('h2:contains("Editer une observation")')->count());

        $form = $crawler->selectButton('Valider')->form();

        $form['appbundle_observation[title]'] = 'test';

        $crawler = $client->submit($form);

        $this->assertEquals(1, $crawler->filter('td:contains("test")')->count());

        // Retour Etat initial

        $client->click($linkObservations->link());

        $crawler = $client->click($linkEditer->link());

        $this->assertEquals(1, $crawler->filter('h2:contains("Editer une observation")')->count());

        $form = $crawler->selectButton('Valider')->form();

        $form['appbundle_observation[title]'] = 'Observation d\'un Chevalier stagnatile';

        $crawler = $client->submit($form);

        $this->assertEquals(1, $crawler->filter('td:contains("Observation d\'un Chevalier stagnatile")')->count());

    }
}