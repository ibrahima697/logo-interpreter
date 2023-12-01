<?php
namespace App\Services;


class InterpreteurLogo
{

 

    protected $command = array (
        "Avancer" => "AV",
        "Reculer"=> "RE",
        "Droite"=> "TD",
        "Gauche"=> "TG",
        "La tortue ne laisse pas de trace"=> "LC",
        "La tortue laisse une trace"=> "BC",
        "La tortue est cachée"=> "CT",
        "La tortue est visible"=> "MC",
        "Efface l'ecran et replace la tortue au centre"=> "VE",
        "Efface l'ecran sans changer la position de la tortue"=> "NETTOIE",
        "Rplace la tortue au centre"=> "ORIGINE",
        "Efface la console"=> "VT",
        "Change la couleur du trait"=> "FCC",
        "Change la couleur du fond"=> "FCB",
        "Fixe l'angle de la tortue"=> "FCAP",
        "Retourne l'angle de la tortue"=> "CAP",
        "Position de la tortue x,y"=> "FPOS",
        "Retourne la position"=> "POS",
    );
    protected $positionX = 0;
    protected $positionY = 0;
    protected $angle = 0;
    protected $isVisible = true;
    protected $isHidden = true ;
    protected $isDrawing = true; // Nouvelle variable pour gérer le mode de dessin


    public function avancer()
    {
        $newX = $this->positionX + cos(deg2rad($this->angle));
        $newY = $this->positionY + sin(deg2rad($this->angle));

        if ($this->isDrawing) {
            // Logique pour dessiner une ligne de ($this->positionX, $this->positionY) à ($newX, $newY)
        }

        $this->positionX = $newX;
        $this->positionY = $newY;
    }

    public function reculer()
    {
        $newX = $this->positionX - cos(deg2rad($this->angle));
        $newY = $this->positionY - sin(deg2rad($this->angle));

        if ($this->isDrawing) {
            // Logique pour dessiner une ligne de ($this->positionX, $this->positionY) à ($newX, $newY)
        }

        $this->positionX = $newX;
        $this->positionY = $newY;
    }

    public function tournerDroite()
    {
        $this->angle += 90; // Tourne de 90 degrés vers la droite
    }

    public function tournerGauche()
    {
        $this->angle -= 90; // Tourne de 90 degrés vers la gauche
    }

    public function laTortueNeLaissePasDeTrace()
    {
        $this->isDrawing = false; // Passe en mode "sans trace"
    }
   
    // ... Ajoutez des méthodes pour chaque commande ...

    public function getPosition()
    {
        return ['x' => $this->positionX, 'y' => $this->positionY];
    }

    public function getAngle()
    {
        return $this->angle;
    }

    public function isVisible()
    {

        return $this->isVisible;
    }
    public function isHidden()
    {
        return $this->isHidden;
    }
    public function clearConsole()
    {
        $this->console = [];
        $this->positionX = 0;
        $this->positionY = 0;
    }

    // Ajoutez d'autres méthodes pour prendre en charge l'utilisation de variables et la définition de procédures







}
