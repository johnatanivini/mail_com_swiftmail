<?php

namespace App;


/**
 * Dependências swiftmailer
 * composer require "swiftmailer/swiftmailer"
 *
 * Class Mail
 * @package App
 */

class Email
{

    protected $host;
    protected $usr;
    protected $pwd;
    protected $is_debug;
    protected $secure;
    protected $is_html;
    protected $titulo;
    protected $config;
    protected $mail;
    protected $transporte;
    protected $arquivo_configuracao;
    protected $template_email;
    public $from = [];//["exemplo1@rtdbrasil.org.br","exemplo2@rtdbrasil.org.br"=>"Nome do email"]
    public $to = []; //["exemplo1@rtdbrasil.org.br","exemplo2@rtdbrasil.org.br"=>"Nome do email"]
    public $body;
    public $subject;
    public $nome;

    /**
     * Mail constructor.
     *
     * $from = de onde o(s) email partiu
     * $to = para qual(quais) email será enviado
     * $assunto = assunto do email
     * $template = template que será usado para corpo do email
     * $parametros = um array de chaves e valores, onde as chaves serã as tags que serão
     *               substituidas pelos seus respectivos valores
     *
     * @param array $from
     * @param array $to
     * @param string $assunto
     * @param string $template
     * @param array $parametros
     */
    public function __construct(array $from, array $to,$assunto,$template,array $parametros)
    {

        $this->arquivo_configuracao =require __DIR__ . "/../configuracoes.php";
        $this->template_email = $this->arquivo_configuracao['settings']['view']['template_email'];
        $this->config = $this->arquivo_configuracao['settings']['email'];

        $this->host = $this->config["host"];
        $this->usr = $this->config["usr"];
        $this->pwd = $this->config["pwd"];
        $this->secure = $this->config["secure"];
        //$this->porta = $this->config["porta"];
        $this->from = $from;
        $this->to = $to;
        $this->subject = $assunto;
        /**
         * Monta template de envio
         */
        $mensagem = $this->getTemplate($template,$parametros);
        $this->body = $mensagem;

    }

    /**
     * Envio do E-mail
     * @return int
     */
    public function sendEmail() {

        /**
         * Documentação na documentação está errada, foi
         * preciso colocar o autoload aqui.
         *
         * https://github.com/swiftmailer/swiftmailer/issues/925
         *
         */
        require_once __DIR__."/../../vendor/autoload.php";

        $transporte = (new \Swift_SmtpTransport($this->host))
            ->setUsername($this->usr)
            ->setPassword($this->pwd);

        $mail = new \Swift_Mailer($transporte);

        $mensagem = (new \Swift_Message($this->subject))
            ->setFrom($this->from)
            ->setTo($this->to)
            ->setBody($this->body,'text/html','utf-8');

        $result = $mail->send($mensagem);

        return $result;
    }

    /**
     *
     * Busca os valores a serem substiruidos
     *
     */
    private function getTemplate($template,array $parametros){

        $string = file_get_contents($this->template_email.$template.".html");

        foreach ($parametros as $key=>$value){
            $string = str_replace($key,$value,$string);
        }

       return $string;
    }
}
/**
 * ex: $email = (new Email(['contato@example.com'],['ccontato@example.com'],'Assunto','template_mail',[
 *      '[EX_CHAVE]'=>'nome'
 *  ]))->sendMail();
 */