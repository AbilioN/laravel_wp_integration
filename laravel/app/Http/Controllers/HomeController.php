<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WordPressSettings;
use App\Models\WordPressPost;
use App\Models\WordPressMenu;

class HomeController extends Controller
{
    /**
     * Exibir a página inicial do WordPress
     */
    public function index()
    {
        // Buscar configurações do site
        $siteTitle = WordPressSettings::getSiteTitle();
        $siteDescription = WordPressSettings::getSiteDescription();
        
        // Verificar se deve mostrar posts ou página na inicial
        if (WordPressSettings::isPostsOnFront()) {
            // Mostrar posts na página inicial
            $posts = WordPressSettings::getRecentPostsForHome(6);
            $homePage = null;
            $viewType = 'posts';
        } else {
            // Mostrar página específica na inicial
            $homePage = WordPressSettings::getHomePage();
            $posts = WordPressSettings::getRecentPostsForHome(3);
            $viewType = 'page';
        }

        // Buscar páginas para o menu lateral
        $sidebarPages = WordPressMenu::getNavigationPages()->take(5);
        
        return view('home', compact(
            'siteTitle',
            'siteDescription', 
            'homePage',
            'posts',
            'sidebarPages',
            'viewType'
        ));
    }

    /**
     * API para buscar dados da página inicial
     */
    public function apiHome()
    {
        $data = [
            'site' => [
                'title' => WordPressSettings::getSiteTitle(),
                'description' => WordPressSettings::getSiteDescription(),
                'url' => WordPressSettings::getSiteUrl(),
            ],
            'isPostsOnFront' => WordPressSettings::isPostsOnFront(),
            'isPageOnFront' => WordPressSettings::isPageOnFront(),
        ];

        if (WordPressSettings::isPostsOnFront()) {
            $data['posts'] = WordPressSettings::getRecentPostsForHome(6);
            $data['homePage'] = null;
        } else {
            $data['homePage'] = WordPressSettings::getHomePage();
            $data['posts'] = WordPressSettings::getRecentPostsForHome(3);
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
} 