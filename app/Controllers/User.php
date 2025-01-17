<?php
/**
 * Created by PhpStorm.
 * User: lat
 * Date: 04/01/20
 * Time: 00:55
 */

namespace App\Controllers;

use App\Models\UserModel;

class User extends BaseController
{
    /**
     * Page Formulaire de connexion
     */
    public function login()
    {
        $session = session();
        $data['flashMessages'] = $session->getFlashdata();
        
        if($session->get('mail')) {
            return redirect('home');
        }
        
        // si on a validé le formulaire de connexion
        if ($this->request->getPost()){
            // récupération du modèle User
            $userModel = new UserModel();
            
            $mail = $this->request->getPost('mail');
            $password = $this->request->getPost('password');

            // si le mail existe en base
           if($userModel->login($mail, $password) == true){
                
                return redirect('home');
           }
           else {
                $errors[] = 'Mail ou mot de passe incorrect';

                $session->setFlashdata('errors', $errors);
                return redirect('login');
           }
        }

        $data['title'] = 'Connexion';

        echo view('login/header', $data);
        echo view('login/login', $data);
    }

    /**
     * Page d'accueil quand user connecté
     */
    public function home()
    {
        $data['title'] = "Page d'accueil";

        echo view('user/header', $data);
        echo view('user/home');
    }

    /**
     * Déconnexion
     */
    public function logout()
    {
        $session = session();

        // suppression du user connecte
        $session->remove('user');

        // redirection vers la page de connexion
        return redirect('login');
    }

    /**
     * Page Mot de passe oublié
     */
    public function forgotPassword()
    {
        $data['title'] = 'Mot de passe oublié';

        echo view('login/header', $data);
        echo view('login/forgot-password');
    }

    /**
     * Page de création d'un utilisateur et traitement formulaire
     */
    public function createUser()
    {
        // session temporaire pour les messages d'erreur temporaire
        $session = session();
        $data['flashMessages'] = $session->getFlashdata();

        // si on a validé le formulaire d'ajout d'un utilisateur
        if ($this->request->getPost())
        {
            // récupération du modèle User
            $userModel = new UserModel();

            // initialise la session
            $session = \Config\Services::session();

            // Classe Validation instanciée (relative aux règles de validation des formulaires)
            helper('form');

            //Récupérer les données saisies envoyées en POST
            $email    = $this->request->getPost('mail');
            $password = $this->request->getPost('password1');

            $rules = [
                'mail' => ['label' => 'Mail', 'rules' => 'required|valid_email|is_unique[utilisateur.mail]'],
                'password1' => ['label' => 'Mot de passe', 'rules' => 'required|min_length[5]'],
                'password2' => ['label' => 'Mot de passe 2', 'rules' => 'required|matches[password1]'],
            ];

            // on prépare les données qu'on va envoyer en base de données
            $data = [
                    'mail'     => $email,
                    'motPasse' => $password,
            ];

            // SI pas d'erreur de validation de formulaire
            if (!$this->validate($rules))
            {
                // on récupère les erreurs du formulaire et on les met en session temporaire (flashdata)
                $session->setFlashdata('errors', $this->validator->getErrors());
            }
            else
            {
                // message de succès
                $session->setFlashdata('success', 'Utilisateur ajouté.');

                // ajout des données en base
                $userModel->addUser($data);
            }

            // redirection sur la page
            return redirect('user/create');
        }

        $data['title'] = "Création d'un utilisateur";

        echo view('login/header', $data);
        echo view('login/create-user', $data);
    }
}
