<?php

namespace App\Http\Controllers;

use App\Models\LogoCommand;
use Illuminate\Http\Request;
use App\Services\InterpreteurLogo;
use Illuminate\Support\Facades\Storage;


class LogoController extends Controller
{
    protected $currentPosition;
    protected $isVisible = true;
    protected $isHidden = true;
    //protected $isDrawing = true;
    protected $positionX;
    protected $positionY;
    protected $angle;
    protected $color;
    protected $distance;
    protected $canvasWidth;  // Ajustez la valeur en conséquence
    protected $canvasHeight;
    protected $angleInRadians;

    protected $session ;

    public function index(Request $request)
    {
     
        try {
            if ($request->isMethod('post')) {
                $command = $request->input('command');
                $currentPosition = $this->getInitialTurtlePosition();
                $interpretationResult = $this->interpretWithMLLibrary($command, $currentPosition);
    
                // Retourner les données interprétées au format JSON
                return response()->json($interpretationResult);

                // Interpréter la commande avec la bibliothèque ML
                $interpretationResult = $this->interpretWithMLLibrary($command);
                InterpreteurLogo::getInstance()->setX();
                sleep(1);
                InterpreteurLogo::getInstance()->getX();
                return response()->json($this->currentPosition);
            }
        } catch (\Exception $e) {
            \Log::error('Erreur dans LogoController : ' . $e->getMessage());
            return response()->json(['error' => 'Commande non reconnue'], 400);
        }
    }
    private function getInitialTurtlePosition()
    {
        $canvasWidth = 500/* la largeur de votre canvas */;
        $canvasHeight = 500/* la hauteur de votre canvas */;
        // Logique pour obtenir la position initiale de la tortue
        $initialPosition = [
            'x' => $canvasWidth / 2, // Valeur initiale pour la coordonnée x
            'y' => $canvasHeight / 2, // Valeur initiale pour la coordonnée y
            'angle' => 90, // Valeur initiale pour l'angle
        ];

        return $initialPosition;
    }
    public function __construct()
    {
        if (session()->has('currentPosition')) {
            $this->currentPosition = session()->get('currentPosition');
        }
        else {
        $this->currentPosition = $this->getInitialTurtlePosition();
        session()->put('currentPosition', $this->currentPosition);
        }    
        }
    
        private function setCurrentPosition($currentPosition)
        {
            // Mettre à jour la session avec la nouvelle position
            session(['currentPosition' => $currentPosition]);
        }
        
        private function getCurrentPosition()
        {
            // Récupérer la position actuelle depuis la session
            return session('currentPosition', $this->getInitialTurtlePosition());
        }
    public function setAbsolutePosition($x, $y)
    {
        // Définir les nouvelles coordonnées absolues par rapport au centre
        $this->positionX = $x;
        $this->positionY = $y;
    
        // Retourner la position actuelle après la modification
        return [
            'x' => $this->positionX,
            'y' => $this->positionY,
            'angle' => $this->angle, // Assurez-vous que vous avez la propriété d'angle dans votre contrôleur
            // ... autres propriétés de position ...
        ];
    }
    private function interpretWithMLLibrary($commands,$currentPosition)
    {
        $parts = explode(' ', $commands);

        // Obtenir la première partie (le nom de la commande)
        $action = strtoupper($parts[0]);
        // ... logique d'exécution ...
        $newPosition = $this->currentPosition;

    // Logique d'interprétation avec la bibliothèque ML
    $commands = [
         "AV" => "Avancer" ,
         "RE" => "Reculer",
         "TD" => "Tourner à Droite",
         "TG" => "Tourner à Gauche",
         "LC" => "La tortue ne laisse pas de trace",
         "BC" => "La tortue laisse une trace",
         "CT" => "La tortue est cachée",
         "MC" => "La tortue est visible",
         "VE" => "Efface l'ecran et replace la tortue au centre",
         "NETTOIE" => "Efface l'ecran sans changer la position de la tortue",
         "ORIGINE" => "Replace la tortue au centre",
         "VT" => "Efface la console",
         "FCC" => "Change la couleur du trait",
         "FCB" => "Change la couleur du fond",
         "FCAP" => "Fixe l'angle de la tortue",
         "CAP" => "Retourne l'angle de la tortue",
         "FPOS" => "Position de la tortue x,y",
         "POS" => "Retourne la position",
         'REPETE' => 'Répéter',
    ];
    if (array_key_exists($action, $commands)) {
    switch ($action) {
        case 'AV':
            // Logique pour avancer
            $distance = isset($parts[1]) ? intval($parts[1]) : 1; // Par défaut, avancer de 1 unité
            $newPosition = $this->moveForward($distance);
            break;

        case 'RE':
            // Logique pour reculer
            $distance = isset($parts[1]) ? intval($parts[1]) :1;
            $newPosition = $this->moveBack($distance);
            break;
        case 'TD':
              // Logique pour tourner à droite
            $angle = isset($parts[1]) ? intval($parts[1]) : 90; // Par défaut, tourner de 45 ou 90 degrés selon penda
            $newPosition = $this->turnRight($angle);
            break;
        case 'TG':
            $angle = isset($parts[1]) ? intval($parts[1]) :90;
            $newPosition = $this->turnLeft($angle);
            break;
            case 'LC':
                $distance = isset($parts[1]) ? intval($parts[1]) : 1;
                $newPosition = $this->noTrack($newPosition, $currentPosition['angle']);
                break;
            
            case 'BC':
                $distance = isset($parts[1]) ? intval($parts[1]) : 1;
                $newPosition = $this->trackBack($newPosition, $currentPosition['angle']);
                break;
        case 'CT':
            $interpretationResult = ['command' => 'CT'];
            break;    
        case 'MC':
            $interpretationResult = ['command' => 'MC'];
            break;
            case 'VE':
                $newPosition = $this->clearScreenAndMoveToCenter($newPosition);
                break;
        case 'NETTOIE':
            $newPosition = $this->clearScreenWithoutMoving($newPosition);
            break;
        case 'VT':
            $newPosition = $this->clearCanvas($currentPosition);
        break;
        case 'ORIGINE':
            $newPosition = $this->moveToCenter($newPosition, $canvasWidth, $canvasHeight);
            break;
        case 'FCB':
            if (count($parts) >= 2) {
                $color = $parts[1]; // Récupérez la couleur spécifiée dans la commande
                $newPosition = $this->changeBackgroundColor($color, $currentPosition['backgroundcolor']);
            } else {
                // Gérez le cas où la commande ne contient pas suffisamment d'arguments
                return response()->json(['error' => 'La commande FCB nécessite une couleur en argument'], 400);
            }
            break;
        case 'FCC':
            $color = $parts[1]; // Récupérer la couleur spécifiée dans la commande
            $newPosition = $this->setPenColor($color, $currentPosition);
            break;
        case 'CAP':
            $angle =  $parts[1];
            $currentAngle =$this->angle;
            return $currentAngle;            
            break;
        case 'FCAP':    
                $newPosition = $this->getInitialTurtlePosition();
                 break;
        case 'FPOS':
            $x = $parts[1]; // Récupérer la coordonnée x spécifiée dans la commande
            $y = $parts[2]; // Récupérer la coordonnée y spécifiée dans la commande
            $newPosition = $this->setAbsolutePosition($x, $y);
            break;
        case 'POS':
                // Logique pour retourner la position de la tortue
                $currentPosition = [
                    'x' => $this->positionX,
                    'y' => $this->positionY,
                    'angle' => $this->angle,
                    // ... autres propriétés de position ...
                ];
            
                return $currentPosition;
        break;
        case 'REPETE':
            // Logique pour répéter un bloc d'instructions
            $repeatCount = isset($parts[1]) ? intval($parts[1]) : 1;
            $block = implode(' ', array_slice($parts, 3, -1)); // Obtenez le bloc d'instructions entre crochets
            $newPosition = $this->repeatBlock($block, $repeatCount, $currentPosition);
            break;

        default:
            // Cas par défaut pour les commandes non reconnues
            $newPosition = $currentPosition;  

        }
    } else { 
    // Retourner le résultat de l'interprétation
    return 'commande non reconnue';
    }
        return $newPosition;
  

    // Remplacer ceci par votre logique réelle
    
    }
    private function repeatBlock($block, $repeatCount, $currentPosition)
    {
        $newPosition = $currentPosition;

        for ($i = 0; $i < $repeatCount; $i++) {
            // Interprétez chaque commande dans le bloc
            $commandsInBlock = explode(' ', $block);
            foreach ($commandsInBlock as $command) {
                $newPosition = $this->interpretSingleCommand($command, $newPosition);
            }
        }

        return $newPosition;
    }

     private function interpretSingleCommand($command, $currentPosition)
    {
        // Logique pour interpréter une seule commande
        $command = strtoupper(trim($command));

        // Logique pour interpréter une seule commande
        switch ($command) {
            case 'AV':
                $newPosition = $this->moveForward($distance, 10);
                break;
            case 'RE':
                $newPosition = $this->moveBack($distance, 10);
                break;
            case 'TD':
                $angle = 45;
                $newPosition = $this->turnRight($angle,90);
                break;
            case 'TG':
                $angle = 45;
                $newPosition = $this->turnLeft($angle,90);
                break;
            

            default:
                // Cas par défaut pour les commandes non reconnues
                $newPosition = $currentPosition;
                break;
        }

        return $newPosition;
    } 
    


    private function moveForward($distance)
{
    $this->currentPosition = $this->getCurrentPosition();

    // Calculer les nouvelles coordonnées en fonction de l'angle et de la distance
    if (array_key_exists('x', $this->currentPosition)) {
        $this->currentPosition['x'] += $distance;
    }

    // Mise à jour de la position actuelle
    $this->setCurrentPosition($this->currentPosition);

    return $this->currentPosition;
}

private function moveBack($distance)
{
    $this->currentPosition = $this->getCurrentPosition();

    // Calculer les nouvelles coordonnées en fonction de l'angle et de la distance
    if (array_key_exists('x', $this->currentPosition)) {
        $this->currentPosition['x'] -= $distance;
    }

    // Mise à jour de la position actuelle
    $this->setCurrentPosition($this->currentPosition);

    return $this->currentPosition;
}

    
    private function turnRight($angle)
{
    $this->currentPosition = $this->getCurrentPosition();

    // Calculer le nouvel angle en tournant à droite sur place
    $newAngle = ($this->currentPosition['angle'] + $angle) % 360;

    // Mettre à jour l'angle dans la nouvelle position
    $this->currentPosition['angle'] = $newAngle;

    // Mise à jour de la position actuelle
    $this->setCurrentPosition($this->currentPosition);

    return $this->currentPosition;
}

private function turnLeft($angle)
{
    $this->currentPosition = $this->getCurrentPosition();

    // Calculer le nouvel angle en tournant à gauche sur place
    $newAngle = ($this->currentPosition['angle'] - $angle) % 360;

    // Mettre à jour l'angle dans la nouvelle position
    $this->currentPosition['angle'] = $newAngle;

    // Mise à jour de la position actuelle
    $this->setCurrentPosition($this->currentPosition);

    return $this->currentPosition;
}

private function noTrack($currentPosition, $angle)
{
    // Utiliser la position actuelle passée en paramètre
    $newPosition = $currentPosition;

    // Marquer la tortue pour ne pas laisser de trace
    //$newPosition['isDrawing'] = false;

    return $newPosition;
}


private function trackBack($currentPosition, $angle)
{
    // Utiliser la position actuelle passée en paramètre
    $newPosition = $currentPosition;

    // Marquer la tortue pour laisser une trace
    //$newPosition['isDrawing'] = true;

    return $newPosition;
}

    private function clearScreen($currentPosition)
    {
        // Effacer l'écran en replaçant la tortue au centre
        $newPosition['x'] = 0;
        $newPosition['y'] = 0;
        $newPosition['angle'] = 0;
        $newPosition['isVisible'] = true;
    

        return $newPosition;
    }
    private function clearScreenAndMoveToCenter()
    {
        // Logique pour effacer l'écran
        $this->clearCanvas(); // Appel de la fonction pour effacer le canvas
    
        // Logique pour replacer la tortue au centre
        $newPosition = $this->moveToCenter($newPosition);
    
        return $newPosition;
    }
private function clearScreenWithoutMoving()
{
    // Logique pour effacer l'écran sans changer la position
    $this->clearCanvas(); // Appel de la fonction pour effacer le canvas sans changer la position

    // Aucune modification de position à effectuer ici

    return $this->currentPosition; // Ou retournez une réponse appropriée selon votre logique penda
}


private function clearCanvas()
{
    // Logique pour effacer le canvas

    // Vous devez définir la largeur et la hauteur du canvas ici
        $canvasWidth = 500  ;
        $canvasHeight = 500 ; 
  
    $newPosition['x'] = $canvasWidth / 2;
    $newPosition['y'] = $canvasHeight / 2;
    $newPosition['angle'] = 0;
    $newPosition['isVisible'] = false;
  

    return $newPosition;
}

private function moveToCenter($newPosition)
{
    $canvasWidth = 500; // Ajustez la valeur en conséquence
    $canvasHeight = 500; // Ajustez la valeur en conséquence

    // Logique pour replacer la tortue au centre
    $newPosition['x'] = $this->canvasWidth / 2;
    $newPosition['y'] = $this->canvasHeight / 2;
    $newPosition['angle'] = 0; 
    $newPosition['isVisible'] = true;
 

    return $newPosition;
}

    private function changeLineColor($color, $currentPosition)
    {
        // Changer la couleur du trait
        $newPosition['lineColor'] = $color;

        // Copier les autres indicateurs sans les modifier
        $newPosition['x'] = $currentPosition['x'];
        $newPosition['y'] = $currentPosition['y'];
        $newPosition['angle'] = $currentPosition['angle'];
      
        $newPosition['isVisible'] = $currentPosition['isVisible'];

        return $newPosition;
    }

    private function getBackgroundColor($color)
    {
        // Vérifiez d'abord si la clé 'backgroundColor' existe dans le tableau
        if (array_key_exists('backgroundColor', $color)) {
            return $color['backgroundColor'];
        }

        // Par défaut, retournez une valeur par défaut ou lancez une exception selon vos besoins
        return '#RRGGBB'; // Valeur par défaut blanche, remplacez-la par ce que vous préférez
    }

    // Utilisation dans votre méthode changeBackgroundColor
    private function changeBackgroundColor($color, $currentPosition)
    {
        $backgroundColor = $this->getBackgroundColor($color);
    // Changer la couleur du fond
    if ($color) {
        $newPosition['backgroundColor'] = $color;
    } else {
        // Si aucune couleur n'est spécifiée, conservez la couleur actuelle
    }

    // Copiez les autres indicateurs sans les modifier
    $newPosition['x'] = $currentPosition['x'];
    $newPosition['y'] = $currentPosition['y'];
    $newPosition['angle'] = $currentPosition['angle'];
    $newPosition['isVisible'] = $currentPosition['isVisible'];

    // Set la couleur de fond du canvas
    return $newPosition;
    }

    private function setPenColor($color, $currentPosition)
    {
        $newPosition['lineColor'] = $color;

        // Copier les autres indicateurs sans les modifier
        $newPosition['x'] = $currentPosition['x'];
        $newPosition['y'] = $currentPosition['y'];
        $newPosition['angle'] = $currentPosition['angle'];
        $newPosition['isVisible'] = true;
    
        // Retourner la position actuelle de la tortue ou d'autres données nécessaires
        return $newPosition;
    }

    private function setAbsoluteAngle($angle)
    {
        // Implémenter la logique pour fixer l'angle de la tortue de façon absolue
        // S'assurer que l'angle est dans la plage [0, 360)
        $angle = ($angle + 360) % 360;

        // Mise à jour de l'angle dans la nouvelle position
        $newPosition['angle'] = $angle;

        // Copier les autres coordonnées sans les modifier
        $newPosition['x'] = $currentPosition['x'];
        $newPosition['y'] = $currentPosition['y'];
        $newPosition['isVisible'] = true;
      
        // s'assurer de valider et formater correctement l'angle
        $newPosition['angle'] = $currentPosition['angle'];

        // ...

        // Retourner la position actuelle de la tortue ou d'autres données nécessaires
        return $newPosition;
    }

}
