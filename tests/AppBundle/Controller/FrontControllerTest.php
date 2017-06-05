<?php

namespace tests\AppBundle\Controller;





use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class FrontControllerTest extends WebTestCase
{

    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(1, $crawler->filter('h3:contains("Les dernières observations")')->count());
    }
    public function testLienAccueilDansIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $linkAccueil = $crawler->filter('a:contains(" Accueil")');
        $crawler =$client->click($linkAccueil->link());

        $this->assertEquals(1, $crawler->filter('h3:contains("Les dernières observations")')->count());
    }
    public function testLienNosAmisLesOiseauxDansIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $linkAccueil = $crawler->filter('a:contains("Nos Amis les Oiseaux")');
        $crawler =$client->click($linkAccueil->link());

        $this->assertEquals(1, $crawler->filter('h3:contains("Les dernières observations")')->count());
    }
    public function testLienEspeceDansIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $linkEspece = $crawler->filter('a:contains(" Espèces")');
        $crawler =$client->click($linkEspece->link());


        $this->assertEquals(1, $crawler->filter('p:contains("Total d\'espèces")')->count());
    }
    public function testLienObservationsDansIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $linkObservations = $crawler->filter('a:contains(" Observations")');
        $crawler =$client->click($linkObservations->link());


        $this->assertEquals(1, $crawler->filter('p:contains("Total d\'observations")')->count());
    }
    public function testLienConnexionDansIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $linkConnexion = $crawler->filter('a:contains("Connexion")');
        $crawler =$client->click($linkConnexion->link());


        $this->assertEquals(1, $crawler->filter('h2:contains("Identification")')->count());
    }
    public function testLienVoirLaFiche1DansIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/viewoneobservation/1');


        $this->assertEquals(2, $crawler->filter('td:contains("Tringa stagnatilis")')->count());
    }
    public function testLienVoirLaFiche2DansIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/viewoneobservation/2');


        $this->assertEquals(2, $crawler->filter('td:contains("Anthus richardi")')->count());
    }
    public function testLienVoirLaFiche3DansIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/viewoneobservation/3');


        $this->assertEquals(1, $crawler->filter('td:contains("Observation d\'un Pipit de Richard")')->count());
    }
    public function testLienQuiSommesNousDansIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/learnmore');


        $this->assertEquals(1, $crawler->filter('h3:contains("L\'association NAO")')->count());
    }
    public function testLienContactEtEnvoieDuFormulaireDeContactDansIndex()
    {

        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(1, $crawler->filter('h4:contains("Formulaire de contact")')->count());


        $form = $crawler->selectButton('boutonEnvoieFormContact')->form();

        $form['appbundle_contact[name]']       = 'Nom';
        $form['appbundle_contact[email]']      = 'Email@email.com';
        $form['appbundle_contact[object]']    = 'Objet';
        $form['appbundle_contact[content]']       = 'Message';
        //$form['appbundle_contact[_token]']       = 'U8ljTOxOjQx_bpcUTmxgGyaK9HOy5Ix-4431jvUgE6c';

        $client->submit($form);

        $client->followRedirects();

        $this->assertEquals(1, $crawler->filter('h3:contains("Les dernières observations")')->count());

    }
    public function testLienInscriptionEtEnvoieDuFormulaireInscriptionPuisRetourEtatInitial()
    {

        $client = static::createClient();

        $crawler = $client->request('GET', '/register/');

        $this->assertEquals(1, $crawler->filter('label:contains("Adresse e-mail")')->count());

        $form = $crawler->selectButton('boutonInscription')->form();

        $form['fos_user_registration_form[email]']       = 'Email@email.com';
        $form['fos_user_registration_form[username]']      = 'Username';
        $form['fos_user_registration_form[plainPassword][first]']    = 'test';
        $form['fos_user_registration_form[plainPassword][second]']       = 'test';
        $form['fos_user_registration_form[name]']       = 'Name';
        //$form['fos_user_registration_form[_token]']      = '2IMvHT2H9abN2o2pR5d7eLUX_YMCtf_1lVm1na9X8r0';


        $client->submit($form);

        $client->followRedirects();

        $this->assertTrue($client->getResponse()->isRedirect('/register/check-email'));

        // Retour Etat initial

        $crawler = $client->request('GET', '/');

        $linkConnexion = $crawler->filter('a:contains("Connexion")');

        $crawler = $client->click($linkConnexion->link());

        $form = $crawler->selectButton('_submit')->form();

        $form['_username'] = 'admin';
        $form['_password'] = 'admin';

        $client->submit($form);

        $crawler = $client->request('GET', '/admin');

        $linkUtilisateur = $crawler->filter('a:contains("Utilisateurs")');

        $crawler = $client->click($linkUtilisateur->link());

        $linkDelete = $crawler->filter('a.deleteUser')->eq('4');

        $crawler = $client->click($linkDelete->link());

        $this->assertEquals(1, $crawler->filter('h2:contains("Supprimer un utilisateur")')->count());

        $formDelete = $crawler->selectButton('Supprimer')->form();

        $crawler = $client->submit($formDelete);

        $this->assertEquals(0,$crawler->filter('td:contains("Username")')->count());

    }
    public function testLienInscriptionEtEnvoieDuFormulaireInscriptionAvecMdpErrone()
    {

        $client = static::createClient();

        $crawler = $client->request('GET', '/register/');

        $this->assertEquals(1, $crawler->filter('label:contains("Adresse e-mail")')->count());

        $form = $crawler->selectButton('boutonInscription')->form();

        $form['fos_user_registration_form[email]']       = 'Email@email.com';
        $form['fos_user_registration_form[username]']      = 'Username';
        $form['fos_user_registration_form[plainPassword][first]']    = 'test';
        $form['fos_user_registration_form[plainPassword][second]']       = 'testtest';
        $form['fos_user_registration_form[name]']       = 'Name';
        //$form['fos_user_registration_form[_token]']      = '2IMvHT2H9abN2o2pR5d7eLUX_YMCtf_1lVm1na9X8r0';


        $client->submit($form);

        $client->followRedirects();

        $this->assertFalse($client->getResponse()->isRedirect('/register/check-email'));

    }
    public function testFormulaireDeConnexionCompteAdmin()
    {

        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $this->assertEquals(1, $crawler->filter('h2:contains("Identification")')->count());

        $form = $crawler->selectButton('_submit')->form();

        $form['_username']       = 'admin';
        $form['_password']      = 'admin';

        $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertEquals(1, $crawler->filter('a:contains("admin ")')->count());

    }
    public function testFormulaireDeConnexionAvecMdpErrone()
    {

        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $this->assertEquals(1, $crawler->filter('h2:contains("Identification")')->count());

        $form = $crawler->selectButton('_submit')->form();

        $form['_username']       = 'admin';
        $form['_password']      = 'test';

        $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertEquals(0, $crawler->filter('a:contains("admin ")')->count());

    }
    public function testLienVoirLaFiche11DansEspece()
    {
    $client = static::createClient();

    $crawler = $client->request('GET', '/viewonespecies/11');


    $this->assertEquals(1, $crawler->filter('td:contains("Autour australien, Émouchet gris")')->count());
    }
    public function testLienVoirLaFiche14DansEspece()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/viewonespecies/14');


        $this->assertEquals(1, $crawler->filter('td:contains("Accipiter francesii brutus")')->count());
    }
    public function testLienVoirPremiereObservationDansEspece()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'viewallspecies/1');

        $linkFiche = $crawler->filter('a:contains("Observation d\'un Chevalier stagnatile")');
        $crawler = $client->click($linkFiche->link());

        $client->followRedirects();

        $this->assertEquals(1, $crawler->filter('td:contains("Scolopacidae")')->count());

    }
    public function testLienVoirPremiereObservationDansObservation()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'viewallobservations/1');

        $linkFiche = $crawler->filter('a:contains("Observation d\'un Chevalier stagnatile")');
        $crawler = $client->click($linkFiche->link());

        $client->followRedirects();

        $this->assertEquals(1, $crawler->filter('td:contains("Scolopacidae")')->count());

    }
    public function testLienVoirLaFicheDansObservation()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'viewallobservations/1');

        $linkFiche = $crawler->filter('a.lienVoirObservation')->eq('0');
        $crawler = $client->click($linkFiche->link());

        $client->followRedirects();

        $this->assertEquals(1, $crawler->filter('td:contains("Motacillidae")')->count());

    }
    public function testModficationProfilEtRetourEtatInitial()
    {

        $client = static::createClient();

        $client->followRedirects();

        $crawler = $client->request('GET', '/');

        $linkConnexion = $crawler->filter('a:contains("Connexion")');

        $crawler = $client->click($linkConnexion->link());

        $form = $crawler->selectButton('_submit')->form();

        $form['_username'] = 'admin';
        $form['_password'] = 'admin';

        $client->submit($form);

        $crawler = $client->request('GET', '/profile/edit');

        $formEdit = $crawler->selectButton('Mettre à jour')->form();

        $formEdit['fos_user_profile_form[name]'] = 'Pierre Admin';

        $crawler = $client->submit($formEdit);

        $this->assertEquals(1,$crawler->filter('p:contains("Nom d\'utilisateur: admin")')->count());

        // Retour Etat Initial

        $crawler = $client->request('GET', '/profile/edit');

        $formEdit = $crawler->selectButton('Mettre à jour')->form();

        $formEdit['fos_user_profile_form[name]'] = 'Pierre Admino';

        $crawler = $client->submit($formEdit);

        $this->assertEquals(1,$crawler->filter('p:contains("Nom d\'utilisateur: admin")')->count());

    }
    public function testInscriptionNewsletterPuisDesinscription()
    {

        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $form = $crawler->selectButton('Ok')->form();

        $form['appbundle_emailnewsletter[email]']       = 'testemail@test.com';

        $client->submit($form);

        $client->followRedirects();

        $this->assertEquals(1, $crawler->filter('h1:contains("Nos Amis les Oiseaux")')->count());

        $linkConnexion = $crawler->filter('a:contains("Connexion")');

        $crawler = $client->click($linkConnexion->link());

        $form = $crawler->selectButton('_submit')->form();

        $form['_username'] = 'admin';
        $form['_password'] = 'admin';

        $client->submit($form);

        $crawler = $client->request('GET', '/admin/viewallregistered/1');

        $this->assertEquals(1, $crawler->filter('h2:contains("Affichage de tous les inscrits à la newsletter.")')->count());

        $linkDesinscription = $crawler->filter('a:contains("Désinscription")')->eq(0);

        $crawler = $client->click($linkDesinscription->link());

        $this->assertEquals(1, $crawler->filter('h2:contains("Affichage de tous les inscrits à la newsletter.")')->count());


    }
    public function testMesObservationsVoirObsEtVoirFiche()
    {

        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $client->followRedirects();

        $linkConnexion = $crawler->filter('a:contains("Connexion")');

        $crawler = $client->click($linkConnexion->link());

        $form = $crawler->selectButton('_submit')->form();

        $form['_username'] = 'nathalie';
        $form['_password'] = 'nathalie';

        $client->submit($form);

        $crawler = $client->request('GET', '/viewmyobservation/1');

        $this->assertEquals(1, $crawler->filter('h2:contains("Mes Observations")')->count());

        $linkVoirObs = $crawler->filter('a:contains("Voir l\'observation")')->eq(0);

        $crawler = $client->click($linkVoirObs->link());

        $this->assertEquals(1, $crawler->filter('td:contains("Scolopacidae")')->count());

        $crawler = $client->request('GET', '/viewmyobservation/1');

        $this->assertEquals(1, $crawler->filter('h2:contains("Mes Observations")')->count());

        $linkvoirFiche = $crawler->filter('a:contains("Voir la fiche de l\'espèce")')->eq(0);

        $crawler = $client->click($linkvoirFiche->link());

        $this->assertEquals(1, $crawler->filter('td:contains("Scolopacidae")')->count());


    }

}
