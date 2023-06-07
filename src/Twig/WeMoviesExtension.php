<?php

namespace WeMovies\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class WeMoviesExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('rating', [$this, 'renderRating']),
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('render_companies', [$this, 'renderCompanies']),
        ];
    }

    public function renderRating($vote)
    {
        $score = round($vote);
        $html = '<div class="rating text-warning">';
        $html .= str_repeat('<i class="bi-star-fill"></i>', $score / 2);
        if (0 !== $score%2) {
            $html .= '<i class="bi-star-half"></i>';
        }
        $html .= str_repeat('<i class="bi-star"></i>', 5 - ($score / 2));
        $html .= '</div>';

        return $html;
    }

    public function renderCompanies($collection)
    {
        $items = [];
        foreach ($collection as $item):
            $items[] = $item->getName();
        endforeach;

        return implode(', ', $items);
    }
}