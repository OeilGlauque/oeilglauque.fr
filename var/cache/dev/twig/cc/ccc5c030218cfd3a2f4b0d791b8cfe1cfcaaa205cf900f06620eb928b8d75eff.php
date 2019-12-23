<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* oeilglauque/index.html.twig */
class __TwigTemplate_3f988a778b5326b5c25268604b7ccdaa05fa184b8f562c10411ffc0358097566 extends \Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'body' => [$this, 'block_body'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return "base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "oeilglauque/index.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "oeilglauque/index.html.twig"));

        $this->parent = $this->loadTemplate("base.html.twig", "oeilglauque/index.html.twig", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

    }

    // line 3
    public function block_body($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "body"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "body"));

        // line 4
        echo "
        <h1>Festival de l'&OElig;il Glauque</h1>
        <p class=\"lead\">Jeux de rôle et jeux de plateau vous attendent à la seizième édition du Festival de l'Oeil Glauque ! <br /> 
        
        Pendant 48h vous pourrez profiter de séances de jeux de rôle, découvrir notre ludothèque de plus de 150 jeux de plateau ou assister à un concert de métal avec le groupe NOTHING BUT ECHOES. 
        Au cours du week-end n’hésitez pas à prendre part à une murder-party, un escape game, ainsi qu'à des tournois de Magic ou Warhammer. 
        Vous pourrez aussi tout simplement venir profiter de l'ambiance et de nos sandwichs, pizzas, burgers et autres friandises et boissons, 
        notamment nos crêpes et nos fameux 3D6 !
        </p>

        <img src=\"/img/affiche.png\" class=\"affiche\" alt=\"Affiche du festival\" />

";
        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    public function getTemplateName()
    {
        return "oeilglauque/index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  68 => 4,  58 => 3,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends 'base.html.twig' %}

{% block body %}

        <h1>Festival de l'&OElig;il Glauque</h1>
        <p class=\"lead\">Jeux de rôle et jeux de plateau vous attendent à la seizième édition du Festival de l'Oeil Glauque ! <br /> 
        
        Pendant 48h vous pourrez profiter de séances de jeux de rôle, découvrir notre ludothèque de plus de 150 jeux de plateau ou assister à un concert de métal avec le groupe NOTHING BUT ECHOES. 
        Au cours du week-end n’hésitez pas à prendre part à une murder-party, un escape game, ainsi qu'à des tournois de Magic ou Warhammer. 
        Vous pourrez aussi tout simplement venir profiter de l'ambiance et de nos sandwichs, pizzas, burgers et autres friandises et boissons, 
        notamment nos crêpes et nos fameux 3D6 !
        </p>

        <img src=\"/img/affiche.png\" class=\"affiche\" alt=\"Affiche du festival\" />

{% endblock %}", "oeilglauque/index.html.twig", "/mnt/data/UbuntuFiles/Code/oeilglauque/oeilglauque.fr/templates/oeilglauque/index.html.twig");
    }
}
