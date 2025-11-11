<?php

    class NavBar {
        private $links = [];
        private static $defaultNavLinks = [
            "Home" => ["url" => "/web_src/index.html", "icon" => "fas fa-home"],
            //"About" => ["url" => "/web_src/general/about.php", "icon" => "fas fa-info-circle"],
            //"Games" => ["url" => "/web_src/games.php", "icon" => "fas fa-gamepad"],
            //"Leaderboard" => ["url" => "/web_src/leaderboard.php", "icon" => "fas fa-trophy"],
            //"Login" => ["url" => "/web_src/index.php?page=login", "icon" => "fas fa-key"]
        ];

        public function __construct($installdir="",$links = []) {
            global $_SESSION,$urlForNavBar;
            if (!is_array($links) || count($links) == 0 ){
                $this->links = self::$defaultNavLinks;
                foreach($this->links as $key => $values){
                    $this->links[$key]["url"] = $urlForNavBar.$this->links[$key]["url"];
                }
            } else {
                $this->links = $links;
            }
            
            if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]==true){
                if(isset($_SESSION["isAdmin"]) && $_SESSION["isAdmin"]==1){
                    $this->links["Settings"] = ["url" => $urlForNavBar."/web_src/admin.php?page=settings","icon" => "fas fa-cog"]; 
                }

                //remove login from array of links
                unset($this->links["Login"]);
                //add a logout feature
                $this->links["Logout"] = ["url" => $urlForNavBar."/web_src/index.php?page=logout","icon" => "fas fa-key"]; 
            }
        }

        public function render() {
            global $urlForNavBar;
            $html = "<nav class='navbar navbar-expand-lg navbar bg-blue'>";
            //Logo Link
            $html .= "<a class='navbar-brand' href='".$this->links["Home"]["url"]."'>";
            $html .= "<img id='logo' src='".$urlForNavBar."/web_src/images/logo.png' alt='Logo' width='100px.'>";
            $html .= "</a>";
            $html .= "<div class='collapse navbar-collapse' id='navbarNav'>";            
            $html .= "<ul class='navbar-nav ml-auto'>";
        
            foreach ($this->links as $text => $info) {

                $url = $info['url'];
                $icon = isset($info['icon']) ? "<i class='{$info['icon']}'></i> " : "";
                $html .= "<li class='nav-item'><a class='navbar-link' href='{$url}'>{$icon}{$text}</a></li>";

            }

            $html .= "</ul></div></nav>";
            return $html;

        }

    }