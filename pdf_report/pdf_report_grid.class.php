<?php
class pdf_report_grid
{
   var $Ini;
   var $Erro;
   var $Pdf;
   var $Db;
   var $rs_grid;
   var $nm_grid_sem_reg;
   var $SC_seq_register;
   var $nm_location;
   var $nm_data;
   var $nm_cod_barra;
   var $sc_proc_grid; 
   var $nmgp_botoes = array();
   var $Campos_Mens_erro;
   var $NM_raiz_img; 
   var $Font_ttf; 
   var $cantidad = array();
   var $impuesto = array();
   var $subtotal = array();
   var $total = array();
   var $total1 = array();
   var $d_iddetalle = array();
   var $c_idcliente = array();
   var $c_nombrecli = array();
   var $c_apellidocli = array();
   var $h_nrohab = array();
   var $t_nombretipohab = array();
   var $t_preciotipohab = array();
   var $r_fechares = array();
   var $d_precioreservacion = array();
//--- 
 function monta_grid($linhas = 0)
 {

   clearstatcache();
   $this->inicializa();
   $this->grid();
 }
//--- 
 function inicializa()
 {
   global $nm_saida, 
   $rec, $nmgp_chave, $nmgp_opcao, $nmgp_ordem, $nmgp_chave_det, 
   $nmgp_quant_linhas, $nmgp_quant_colunas, $nmgp_url_saida, $nmgp_parms;
//
   $this->nm_data = new nm_data("es");
   include_once("../_lib/lib/php/nm_font_tcpdf.php");
   $this->default_font = '';
   $this->default_font_sr  = '';
   $this->default_style    = '';
   $this->default_style_sr = 'B';
   $Tp_papel = array(310, 210);
   $old_dir = getcwd();
   $File_font_ttf     = "";
   $temp_font_ttf     = "";
   $this->Font_ttf    = false;
   $this->Font_ttf_sr = false;
   if (empty($this->default_font) && isset($arr_font_tcpdf[$this->Ini->str_lang]))
   {
       $this->default_font = $arr_font_tcpdf[$this->Ini->str_lang];
   }
   elseif (empty($this->default_font))
   {
       $this->default_font = "Times";
   }
   if (empty($this->default_font_sr) && isset($arr_font_tcpdf[$this->Ini->str_lang]))
   {
       $this->default_font_sr = $arr_font_tcpdf[$this->Ini->str_lang];
   }
   elseif (empty($this->default_font_sr))
   {
       $this->default_font_sr = "Times";
   }
   $_SESSION['scriptcase']['pdf_report']['default_font'] = $this->default_font;
   chdir($this->Ini->path_third . "/tcpdf/");
   include_once("tcpdf.php");
   chdir($old_dir);
   $this->Pdf = new TCPDF('P', 'mm', $Tp_papel, true, 'UTF-8', false);
   $this->Pdf->setPrintHeader(false);
   $this->Pdf->setPrintFooter(false);
   if (!empty($File_font_ttf))
   {
       $this->Pdf->addTTFfont($File_font_ttf, "", "", 32, $_SESSION['scriptcase']['dir_temp'] . "/");
   }
   $this->Pdf->SetDisplayMode('real');
   $this->aba_iframe = false;
   if (isset($_SESSION['scriptcase']['sc_aba_iframe']))
   {
       foreach ($_SESSION['scriptcase']['sc_aba_iframe'] as $aba => $apls_aba)
       {
           if (in_array("pdf_report", $apls_aba))
           {
               $this->aba_iframe = true;
               break;
           }
       }
   }
   if ($_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['iframe_menu'] && (!isset($_SESSION['scriptcase']['menu_mobile']) || empty($_SESSION['scriptcase']['menu_mobile'])))
   {
       $this->aba_iframe = true;
   }
   $this->nmgp_botoes['exit'] = "on";
   $this->sc_proc_grid = false; 
   $this->NM_raiz_img = $this->Ini->root;
   $_SESSION['scriptcase']['sc_sql_ult_conexao'] = ''; 
   $this->nm_where_dinamico = "";
   $this->nm_grid_colunas = 0;
   if (isset($_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['campos_busca']) && !empty($_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['campos_busca']))
   { 
       $Busca_temp = $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['campos_busca'];
       if ($_SESSION['scriptcase']['charset'] != "UTF-8")
       {
           $Busca_temp = NM_conv_charset($Busca_temp, $_SESSION['scriptcase']['charset'], "UTF-8");
       }
       $this->d_iddetalle[0] = $Busca_temp['d_iddetalle']; 
       $tmp_pos = strpos($this->d_iddetalle[0], "##@@");
       if ($tmp_pos !== false && !is_array($this->d_iddetalle[0]))
       {
           $this->d_iddetalle[0] = substr($this->d_iddetalle[0], 0, $tmp_pos);
       }
       $this->c_idcliente[0] = $Busca_temp['c_idcliente']; 
       $tmp_pos = strpos($this->c_idcliente[0], "##@@");
       if ($tmp_pos !== false && !is_array($this->c_idcliente[0]))
       {
           $this->c_idcliente[0] = substr($this->c_idcliente[0], 0, $tmp_pos);
       }
       $this->c_nombrecli[0] = $Busca_temp['c_nombrecli']; 
       $tmp_pos = strpos($this->c_nombrecli[0], "##@@");
       if ($tmp_pos !== false && !is_array($this->c_nombrecli[0]))
       {
           $this->c_nombrecli[0] = substr($this->c_nombrecli[0], 0, $tmp_pos);
       }
       $this->c_apellidocli[0] = $Busca_temp['c_apellidocli']; 
       $tmp_pos = strpos($this->c_apellidocli[0], "##@@");
       if ($tmp_pos !== false && !is_array($this->c_apellidocli[0]))
       {
           $this->c_apellidocli[0] = substr($this->c_apellidocli[0], 0, $tmp_pos);
       }
       $this->d_precioreservacion[0] = $Busca_temp['d_precioreservacion']; 
       $tmp_pos = strpos($this->d_precioreservacion[0], "##@@");
       if ($tmp_pos !== false && !is_array($this->d_precioreservacion[0]))
       {
           $this->d_precioreservacion[0] = substr($this->d_precioreservacion[0], 0, $tmp_pos);
       }
   } 
   $this->nm_field_dinamico = array();
   $this->nm_order_dinamico = array();
   $this->sc_where_orig   = $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['where_orig'];
   $this->sc_where_atual  = $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['where_pesq'];
   $this->sc_where_filtro = $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['where_pesq_filtro'];
   $dir_raiz          = strrpos($_SERVER['PHP_SELF'],"/") ;  
   $dir_raiz          = substr($_SERVER['PHP_SELF'], 0, $dir_raiz + 1) ;  
   $this->nm_location = $this->Ini->sc_protocolo . $this->Ini->server . $dir_raiz; 
   $_SESSION['scriptcase']['contr_link_emb'] = $this->nm_location;
   $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['qt_col_grid'] = 1 ;  
   if (isset($_SESSION['scriptcase']['sc_apl_conf']['pdf_report']['cols']) && !empty($_SESSION['scriptcase']['sc_apl_conf']['pdf_report']['cols']))
   {
       $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['qt_col_grid'] = $_SESSION['scriptcase']['sc_apl_conf']['pdf_report']['cols'];  
       unset($_SESSION['scriptcase']['sc_apl_conf']['pdf_report']['cols']);
   }
   if (!isset($_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['ordem_select']))  
   { 
       $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['ordem_select'] = array(); 
   } 
   if (!isset($_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['ordem_quebra']))  
   { 
       $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['ordem_grid'] = "" ; 
       $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['ordem_ant']  = ""; 
       $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['ordem_desc'] = "" ; 
   }   
   if (!empty($nmgp_parms) && $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['opcao'] != "pdf")   
   { 
       $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['opcao'] = "igual";
       $rec = "ini";
   }
   if (!isset($_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['where_orig']) || $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['prim_cons'] || !empty($nmgp_parms))  
   { 
       $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['prim_cons'] = false;  
       $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['where_orig'] = " where c.nombreCli= \"" . $_SESSION['usr_name'] . "\" ;";  
       $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['where_pesq']        = $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['where_orig'];  
       $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['where_pesq_ant']    = $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['where_orig'];  
       $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['cond_pesq']         = ""; 
       $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['where_pesq_filtro'] = "";
   }   
   if  (!empty($this->nm_where_dinamico)) 
   {   
       $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['where_pesq'] .= $this->nm_where_dinamico;
   }   
   $this->sc_where_orig   = $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['where_orig'];
   $this->sc_where_atual  = $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['where_pesq'];
   $this->sc_where_filtro = $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['where_pesq_filtro'];
//
   if (isset($_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['tot_geral'][1])) 
   { 
       $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['sc_total'] = $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['tot_geral'][1] ;  
   }
   $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['where_pesq_ant'] = $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['where_pesq'];  
//----- 
   if (in_array(strtolower($this->Ini->nm_tpbanco), $this->Ini->nm_bases_sybase))
   { 
       $nmgp_select = "SELECT d.idDetalle as d_iddetalle, c.idCliente as c_idcliente, c.nombreCli as c_nombrecli, c.apellidoCli as c_apellidocli, h.nroHab as h_nrohab, t.nombreTipoHab as t_nombretipohab, t.precioTipoHab as t_preciotipohab, str_replace (convert(char(10),r.fechaRes,102), '.', '-') + ' ' + convert(char(8),r.fechaRes,20) as r_fechares, d.precioReservacion as d_precioreservacion from " . $this->Ini->nm_tabela; 
   } 
   elseif (in_array(strtolower($this->Ini->nm_tpbanco), $this->Ini->nm_bases_mysql))
   { 
       $nmgp_select = "SELECT d.idDetalle as d_iddetalle, c.idCliente as c_idcliente, c.nombreCli as c_nombrecli, c.apellidoCli as c_apellidocli, h.nroHab as h_nrohab, t.nombreTipoHab as t_nombretipohab, t.precioTipoHab as t_preciotipohab, r.fechaRes as r_fechares, d.precioReservacion as d_precioreservacion from " . $this->Ini->nm_tabela; 
   } 
   elseif (in_array(strtolower($this->Ini->nm_tpbanco), $this->Ini->nm_bases_mssql))
   { 
       $nmgp_select = "SELECT d.idDetalle as d_iddetalle, c.idCliente as c_idcliente, c.nombreCli as c_nombrecli, c.apellidoCli as c_apellidocli, h.nroHab as h_nrohab, t.nombreTipoHab as t_nombretipohab, t.precioTipoHab as t_preciotipohab, convert(char(23),r.fechaRes,121) as r_fechares, d.precioReservacion as d_precioreservacion from " . $this->Ini->nm_tabela; 
   } 
   elseif (in_array(strtolower($this->Ini->nm_tpbanco), $this->Ini->nm_bases_oracle))
   { 
       $nmgp_select = "SELECT d.idDetalle as d_iddetalle, c.idCliente as c_idcliente, c.nombreCli as c_nombrecli, c.apellidoCli as c_apellidocli, h.nroHab as h_nrohab, t.nombreTipoHab as t_nombretipohab, t.precioTipoHab as t_preciotipohab, r.fechaRes as r_fechares, d.precioReservacion as d_precioreservacion from " . $this->Ini->nm_tabela; 
   } 
   elseif (in_array(strtolower($this->Ini->nm_tpbanco), $this->Ini->nm_bases_informix))
   { 
       $nmgp_select = "SELECT d.idDetalle as d_iddetalle, c.idCliente as c_idcliente, c.nombreCli as c_nombrecli, c.apellidoCli as c_apellidocli, h.nroHab as h_nrohab, t.nombreTipoHab as t_nombretipohab, t.precioTipoHab as t_preciotipohab, EXTEND(r.fechaRes, YEAR TO DAY) as r_fechares, d.precioReservacion as d_precioreservacion from " . $this->Ini->nm_tabela; 
   } 
   else 
   { 
       $nmgp_select = "SELECT d.idDetalle as d_iddetalle, c.idCliente as c_idcliente, c.nombreCli as c_nombrecli, c.apellidoCli as c_apellidocli, h.nroHab as h_nrohab, t.nombreTipoHab as t_nombretipohab, t.precioTipoHab as t_preciotipohab, r.fechaRes as r_fechares, d.precioReservacion as d_precioreservacion from " . $this->Ini->nm_tabela; 
   } 
   $nmgp_select .= " " . $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['where_pesq']; 
   $nmgp_order_by = ""; 
   $campos_order_select = "";
   foreach($_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['ordem_select'] as $campo => $ordem) 
   {
        if ($campo != $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['ordem_grid']) 
        {
           if (!empty($campos_order_select)) 
           {
               $campos_order_select .= ", ";
           }
           $campos_order_select .= $campo . " " . $ordem;
        }
   }
   if (!empty($_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['ordem_grid'])) 
   { 
       $nmgp_order_by = " order by " . $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['ordem_grid'] . $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['ordem_desc']; 
   } 
   if (!empty($campos_order_select)) 
   { 
       if (!empty($nmgp_order_by)) 
       { 
          $nmgp_order_by .= ", " . $campos_order_select; 
       } 
       else 
       { 
          $nmgp_order_by = " order by $campos_order_select"; 
       } 
   } 
   $nmgp_select .= $nmgp_order_by; 
   $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['order_grid'] = $nmgp_order_by;
   $_SESSION['scriptcase']['sc_sql_ult_comando'] = $nmgp_select; 
   $this->rs_grid = $this->Db->Execute($nmgp_select) ; 
   if ($this->rs_grid === false && !$this->rs_grid->EOF && $GLOBALS["NM_ERRO_IBASE"] != 1) 
   { 
       $this->Erro->mensagem(__FILE__, __LINE__, "banco", $this->Ini->Nm_lang['lang_errm_dber'], $this->Db->ErrorMsg()); 
       exit ; 
   }  
   if ($this->rs_grid->EOF || ($this->rs_grid === false && $GLOBALS["NM_ERRO_IBASE"] == 1)) 
   { 
       $this->nm_grid_sem_reg = $this->SC_conv_utf8($this->Ini->Nm_lang['lang_errm_empt']); 
   }  
// 
 }  
// 
 function Pdf_init()
 {
     if ($_SESSION['scriptcase']['reg_conf']['css_dir'] == "RTL")
     {
         $this->Pdf->setRTL(true);
     }
     $this->Pdf->setHeaderMargin(0);
     $this->Pdf->setFooterMargin(0);
     if ($this->Font_ttf)
     {
         $this->Pdf->SetFont($this->default_font, $this->default_style, 12, $this->def_TTF);
     }
     else
     {
         $this->Pdf->SetFont($this->default_font, $this->default_style, 12);
     }
     $this->Pdf->SetTextColor(0, 0, 0);
 }
// 
 function Pdf_image()
 {
   if ($_SESSION['scriptcase']['reg_conf']['css_dir'] == "RTL")
   {
       $this->Pdf->setRTL(false);
   }
   $SV_margin = $this->Pdf->getBreakMargin();
   $SV_auto_page_break = $this->Pdf->getAutoPageBreak();
   $this->Pdf->SetAutoPageBreak(false, 0);
   $this->Pdf->Image($this->NM_raiz_img . $this->Ini->path_img_global . "/sys__NM__menu_img__NM__FacturaHotel.png", "0.000000", "0.000000", "210", "297", '', '', '', false, 300, '', false, false, 0);
   $this->Pdf->SetAutoPageBreak($SV_auto_page_break, $SV_margin);
   $this->Pdf->setPageMark();
   if ($_SESSION['scriptcase']['reg_conf']['css_dir'] == "RTL")
   {
       $this->Pdf->setRTL(true);
   }
 }
// 
//----- 
 function grid($linhas = 0)
 {
    global 
           $nm_saida, $nm_url_saida;
   $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['labels']['d_iddetalle'] = "Id Detalle";
   $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['labels']['c_idcliente'] = "Id Cliente";
   $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['labels']['c_nombrecli'] = "Nombre Cli";
   $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['labels']['c_apellidocli'] = "Apellido Cli";
   $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['labels']['h_nrohab'] = "Nro Hab";
   $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['labels']['t_nombretipohab'] = "Nombre Tipo Hab";
   $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['labels']['t_preciotipohab'] = "Precio Tipo Hab";
   $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['labels']['r_fechares'] = "Fecha Res";
   $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['labels']['d_precioreservacion'] = "Precio Reservacion";
   $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['labels']['cantidad'] = "cantidad";
   $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['labels']['impuesto'] = "impuesto";
   $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['labels']['subtotal'] = "subtotal";
   $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['labels']['total'] = "total";
   $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['labels']['total1'] = "total1";
   $HTTP_REFERER = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : ""; 
   $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['seq_dir'] = 0; 
   $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['sub_dir'] = array(); 
   $this->sc_where_orig   = $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['where_orig'];
   $this->sc_where_atual  = $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['where_pesq'];
   $this->sc_where_filtro = $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['where_pesq_filtro'];
   if (isset($_SESSION['scriptcase']['sc_apl_conf']['pdf_report']['lig_edit']) && $_SESSION['scriptcase']['sc_apl_conf']['pdf_report']['lig_edit'] != '')
   {
       $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['mostra_edit'] = $_SESSION['scriptcase']['sc_apl_conf']['pdf_report']['lig_edit'];
   }
   if (!empty($this->nm_grid_sem_reg))
   {
       $this->Pdf_init();
       $this->Pdf->AddPage();
       if ($this->Font_ttf_sr)
       {
           $this->Pdf->SetFont($this->default_font_sr, 'B', 12, $this->def_TTF);
       }
       else
       {
           $this->Pdf->SetFont($this->default_font_sr, 'B', 12);
       }
       $this->Pdf->Text(0.000000, 0.000000, html_entity_decode($this->nm_grid_sem_reg, ENT_COMPAT, $_SESSION['scriptcase']['charset']));
       $this->Pdf->Output($this->Ini->root . $this->Ini->nm_path_pdf, 'F');
       return;
   }
// 
   $Init_Pdf = true;
   $this->SC_seq_register = 0; 
   while (!$this->rs_grid->EOF) 
   {  
      $this->nm_grid_colunas = 0; 
      $nm_quant_linhas = 0;
      $this->Pdf->setImageScale(1.33);
      $this->Pdf->AddPage();
      $this->Pdf_init();
      $this->Pdf_image();
      while (!$this->rs_grid->EOF && $nm_quant_linhas < $_SESSION['sc_session'][$this->Ini->sc_page]['pdf_report']['qt_col_grid']) 
      {  
          $this->sc_proc_grid = true;
          $this->SC_seq_register++; 
          $this->d_iddetalle[$this->nm_grid_colunas] = $this->rs_grid->fields[0] ;  
          $this->d_iddetalle[$this->nm_grid_colunas] = (string)$this->d_iddetalle[$this->nm_grid_colunas];
          $this->c_idcliente[$this->nm_grid_colunas] = $this->rs_grid->fields[1] ;  
          $this->c_idcliente[$this->nm_grid_colunas] = (string)$this->c_idcliente[$this->nm_grid_colunas];
          $this->c_nombrecli[$this->nm_grid_colunas] = $this->rs_grid->fields[2] ;  
          $this->c_apellidocli[$this->nm_grid_colunas] = $this->rs_grid->fields[3] ;  
          $this->h_nrohab[$this->nm_grid_colunas] = $this->rs_grid->fields[4] ;  
          $this->t_nombretipohab[$this->nm_grid_colunas] = $this->rs_grid->fields[5] ;  
          $this->t_preciotipohab[$this->nm_grid_colunas] = $this->rs_grid->fields[6] ;  
          $this->t_preciotipohab[$this->nm_grid_colunas] =  str_replace(",", ".", $this->t_preciotipohab[$this->nm_grid_colunas]);
          $this->t_preciotipohab[$this->nm_grid_colunas] = (string)$this->t_preciotipohab[$this->nm_grid_colunas];
          $this->r_fechares[$this->nm_grid_colunas] = $this->rs_grid->fields[7] ;  
          $this->d_precioreservacion[$this->nm_grid_colunas] = $this->rs_grid->fields[8] ;  
          $this->d_precioreservacion[$this->nm_grid_colunas] =  str_replace(",", ".", $this->d_precioreservacion[$this->nm_grid_colunas]);
          $this->d_precioreservacion[$this->nm_grid_colunas] = (string)$this->d_precioreservacion[$this->nm_grid_colunas];
          $this->cantidad[$this->nm_grid_colunas] = "";
          $this->impuesto[$this->nm_grid_colunas] = "";
          $this->subtotal[$this->nm_grid_colunas] = "";
          $this->total[$this->nm_grid_colunas] = "";
          $this->total1[$this->nm_grid_colunas] = "";
          $this->d_iddetalle[$this->nm_grid_colunas] = sc_strip_script($this->d_iddetalle[$this->nm_grid_colunas]);
          if ($this->d_iddetalle[$this->nm_grid_colunas] === "") 
          { 
              $this->d_iddetalle[$this->nm_grid_colunas] = "" ;  
          } 
          else    
          { 
              nmgp_Form_Num_Val($this->d_iddetalle[$this->nm_grid_colunas], $_SESSION['scriptcase']['reg_conf']['grup_num'], $_SESSION['scriptcase']['reg_conf']['dec_num'], "0", "S", "2", "", "N:" . $_SESSION['scriptcase']['reg_conf']['neg_num'] , $_SESSION['scriptcase']['reg_conf']['simb_neg'], $_SESSION['scriptcase']['reg_conf']['num_group_digit']) ; 
          } 
          $this->d_iddetalle[$this->nm_grid_colunas] = $this->SC_conv_utf8($this->d_iddetalle[$this->nm_grid_colunas]);
          $this->c_idcliente[$this->nm_grid_colunas] = sc_strip_script($this->c_idcliente[$this->nm_grid_colunas]);
          if ($this->c_idcliente[$this->nm_grid_colunas] === "") 
          { 
              $this->c_idcliente[$this->nm_grid_colunas] = "" ;  
          } 
          else    
          { 
              nmgp_Form_Num_Val($this->c_idcliente[$this->nm_grid_colunas], $_SESSION['scriptcase']['reg_conf']['grup_num'], $_SESSION['scriptcase']['reg_conf']['dec_num'], "0", "S", "2", "", "N:" . $_SESSION['scriptcase']['reg_conf']['neg_num'] , $_SESSION['scriptcase']['reg_conf']['simb_neg'], $_SESSION['scriptcase']['reg_conf']['num_group_digit']) ; 
          } 
          $this->c_idcliente[$this->nm_grid_colunas] = $this->SC_conv_utf8($this->c_idcliente[$this->nm_grid_colunas]);
          $this->c_nombrecli[$this->nm_grid_colunas] = sc_strip_script($this->c_nombrecli[$this->nm_grid_colunas]);
          if ($this->c_nombrecli[$this->nm_grid_colunas] === "") 
          { 
              $this->c_nombrecli[$this->nm_grid_colunas] = "" ;  
          } 
          $this->c_nombrecli[$this->nm_grid_colunas] = $this->SC_conv_utf8($this->c_nombrecli[$this->nm_grid_colunas]);
          $this->c_apellidocli[$this->nm_grid_colunas] = sc_strip_script($this->c_apellidocli[$this->nm_grid_colunas]);
          if ($this->c_apellidocli[$this->nm_grid_colunas] === "") 
          { 
              $this->c_apellidocli[$this->nm_grid_colunas] = "" ;  
          } 
          $this->c_apellidocli[$this->nm_grid_colunas] = $this->SC_conv_utf8($this->c_apellidocli[$this->nm_grid_colunas]);
          $this->h_nrohab[$this->nm_grid_colunas] = sc_strip_script($this->h_nrohab[$this->nm_grid_colunas]);
          if ($this->h_nrohab[$this->nm_grid_colunas] === "") 
          { 
              $this->h_nrohab[$this->nm_grid_colunas] = "" ;  
          } 
          $this->h_nrohab[$this->nm_grid_colunas] = $this->SC_conv_utf8($this->h_nrohab[$this->nm_grid_colunas]);
          $this->t_nombretipohab[$this->nm_grid_colunas] = sc_strip_script($this->t_nombretipohab[$this->nm_grid_colunas]);
          if ($this->t_nombretipohab[$this->nm_grid_colunas] === "") 
          { 
              $this->t_nombretipohab[$this->nm_grid_colunas] = "" ;  
          } 
          $this->t_nombretipohab[$this->nm_grid_colunas] = $this->SC_conv_utf8($this->t_nombretipohab[$this->nm_grid_colunas]);
          $this->t_preciotipohab[$this->nm_grid_colunas] = sc_strip_script($this->t_preciotipohab[$this->nm_grid_colunas]);
          if ($this->t_preciotipohab[$this->nm_grid_colunas] === "") 
          { 
              $this->t_preciotipohab[$this->nm_grid_colunas] = "" ;  
          } 
          else    
          { 
              nmgp_Form_Num_Val($this->t_preciotipohab[$this->nm_grid_colunas], $_SESSION['scriptcase']['reg_conf']['grup_val'], $_SESSION['scriptcase']['reg_conf']['dec_val'], "0", "S", "2", "", "V:" . $_SESSION['scriptcase']['reg_conf']['monet_f_pos'] . ":" . $_SESSION['scriptcase']['reg_conf']['monet_f_neg'], $_SESSION['scriptcase']['reg_conf']['simb_neg'], $_SESSION['scriptcase']['reg_conf']['unid_mont_group_digit']) ; 
          } 
          $this->t_preciotipohab[$this->nm_grid_colunas] = $this->SC_conv_utf8($this->t_preciotipohab[$this->nm_grid_colunas]);
          $this->r_fechares[$this->nm_grid_colunas] = sc_strip_script($this->r_fechares[$this->nm_grid_colunas]);
          if ($this->r_fechares[$this->nm_grid_colunas] === "") 
          { 
              $this->r_fechares[$this->nm_grid_colunas] = "" ;  
          } 
          else    
          { 
               $r_fechares_x =  $this->r_fechares[$this->nm_grid_colunas];
               nm_conv_limpa_dado($r_fechares_x, "YYYY-MM-DD");
               if (is_numeric($r_fechares_x) && strlen($r_fechares_x) > 0) 
               { 
                   $this->nm_data->SetaData($this->r_fechares[$this->nm_grid_colunas], "YYYY-MM-DD");
                   $this->r_fechares[$this->nm_grid_colunas] = html_entity_decode($this->nm_data->FormataSaida($this->nm_data->FormatRegion("DT", "ddmmaaaa")), ENT_COMPAT, $_SESSION['scriptcase']['charset']);
               } 
          } 
          $this->r_fechares[$this->nm_grid_colunas] = $this->SC_conv_utf8($this->r_fechares[$this->nm_grid_colunas]);
          $this->d_precioreservacion[$this->nm_grid_colunas] = sc_strip_script($this->d_precioreservacion[$this->nm_grid_colunas]);
          if ($this->d_precioreservacion[$this->nm_grid_colunas] === "") 
          { 
              $this->d_precioreservacion[$this->nm_grid_colunas] = "" ;  
          } 
          else    
          { 
              nmgp_Form_Num_Val($this->d_precioreservacion[$this->nm_grid_colunas], $_SESSION['scriptcase']['reg_conf']['grup_val'], $_SESSION['scriptcase']['reg_conf']['dec_val'], "0", "S", "2", "", "V:" . $_SESSION['scriptcase']['reg_conf']['monet_f_pos'] . ":" . $_SESSION['scriptcase']['reg_conf']['monet_f_neg'], $_SESSION['scriptcase']['reg_conf']['simb_neg'], $_SESSION['scriptcase']['reg_conf']['unid_mont_group_digit']) ; 
          } 
          $this->d_precioreservacion[$this->nm_grid_colunas] = $this->SC_conv_utf8($this->d_precioreservacion[$this->nm_grid_colunas]);
          if ($this->cantidad[$this->nm_grid_colunas] === "") 
          { 
              $this->cantidad[$this->nm_grid_colunas] = "" ;  
          } 
          else    
          { 
              nmgp_Form_Num_Val($this->cantidad[$this->nm_grid_colunas], $_SESSION['scriptcase']['reg_conf']['grup_num'], $_SESSION['scriptcase']['reg_conf']['dec_num'], "0", "", "1", "", "N:" . $_SESSION['scriptcase']['reg_conf']['neg_num'] , $_SESSION['scriptcase']['reg_conf']['simb_neg'], $_SESSION['scriptcase']['reg_conf']['num_group_digit']) ; 
          } 
          $this->cantidad[$this->nm_grid_colunas] = $this->SC_conv_utf8($this->cantidad[$this->nm_grid_colunas]);
          if ($this->impuesto[$this->nm_grid_colunas] === "") 
          { 
              $this->impuesto[$this->nm_grid_colunas] = "" ;  
          } 
          else    
          { 
              nmgp_Form_Num_Val($this->impuesto[$this->nm_grid_colunas], $_SESSION['scriptcase']['reg_conf']['grup_num'], $_SESSION['scriptcase']['reg_conf']['dec_num'], "0", "", "1", "", "N:" . $_SESSION['scriptcase']['reg_conf']['neg_num'] , $_SESSION['scriptcase']['reg_conf']['simb_neg'], $_SESSION['scriptcase']['reg_conf']['num_group_digit']) ; 
          } 
          $this->impuesto[$this->nm_grid_colunas] = $this->SC_conv_utf8($this->impuesto[$this->nm_grid_colunas]);
          if ($this->subtotal[$this->nm_grid_colunas] === "") 
          { 
              $this->subtotal[$this->nm_grid_colunas] = "" ;  
          } 
          else    
          { 
              nmgp_Form_Num_Val($this->subtotal[$this->nm_grid_colunas], $_SESSION['scriptcase']['reg_conf']['grup_num'], $_SESSION['scriptcase']['reg_conf']['dec_num'], "0", "", "1", "", "N:" . $_SESSION['scriptcase']['reg_conf']['neg_num'] , $_SESSION['scriptcase']['reg_conf']['simb_neg'], $_SESSION['scriptcase']['reg_conf']['num_group_digit']) ; 
          } 
          $this->subtotal[$this->nm_grid_colunas] = $this->SC_conv_utf8($this->subtotal[$this->nm_grid_colunas]);
          if ($this->total[$this->nm_grid_colunas] === "") 
          { 
              $this->total[$this->nm_grid_colunas] = "" ;  
          } 
          else    
          { 
              nmgp_Form_Num_Val($this->total[$this->nm_grid_colunas], $_SESSION['scriptcase']['reg_conf']['grup_num'], $_SESSION['scriptcase']['reg_conf']['dec_num'], "0", "", "1", "", "N:" . $_SESSION['scriptcase']['reg_conf']['neg_num'] , $_SESSION['scriptcase']['reg_conf']['simb_neg'], $_SESSION['scriptcase']['reg_conf']['num_group_digit']) ; 
          } 
          $this->total[$this->nm_grid_colunas] = $this->SC_conv_utf8($this->total[$this->nm_grid_colunas]);
          if ($this->total1[$this->nm_grid_colunas] === "") 
          { 
              $this->total1[$this->nm_grid_colunas] = "" ;  
          } 
          else    
          { 
              nmgp_Form_Num_Val($this->total1[$this->nm_grid_colunas], $_SESSION['scriptcase']['reg_conf']['grup_num'], $_SESSION['scriptcase']['reg_conf']['dec_num'], "0", "", "1", "", "N:" . $_SESSION['scriptcase']['reg_conf']['neg_num'] , $_SESSION['scriptcase']['reg_conf']['simb_neg'], $_SESSION['scriptcase']['reg_conf']['num_group_digit']) ; 
          } 
          $this->total1[$this->nm_grid_colunas] = $this->SC_conv_utf8($this->total1[$this->nm_grid_colunas]);
            $cell_d_idDetalle = array('posx' => '43', 'posy' => '144.1360041666485', 'data' => $this->d_iddetalle[$this->nm_grid_colunas], 'width'      => '0', 'align'      => 'L', 'font_type'  => 'Helvetica', 'font_size'  => '11', 'color_r'    => '255', 'color_g'    => '255', 'color_b'    => '255', 'font_style' => B);
            $cell_c_idCliente = array('posx' => '172.50833333331158', 'posy' => '65.76536249999171', 'data' => $this->c_idcliente[$this->nm_grid_colunas], 'width'      => '0', 'align'      => 'L', 'font_type'  => 'Helvetica', 'font_size'  => '11', 'color_r'    => '0', 'color_g'    => '0', 'color_b'    => '0', 'font_style' => $this->default_style);
            $cell_c_nombreCli = array('posx' => '158', 'posy' => '28.40', 'data' => $this->c_nombrecli[$this->nm_grid_colunas], 'width'      => '0', 'align'      => 'L', 'font_type'  => 'Helvetica', 'font_size'  => '11', 'color_r'    => '0', 'color_g'    => '0', 'color_b'    => '0', 'font_style' => $this->default_style);
            $cell_c_apellidoCli = array('posx' => '167', 'posy' => '28.40', 'data' => $this->c_apellidocli[$this->nm_grid_colunas], 'width'      => '0', 'align'      => 'L', 'font_type'  => 'Helvetica', 'font_size'  => '11', 'color_r'    => '0', 'color_g'    => '0', 'color_b'    => '0', 'font_style' => $this->default_style);
            $cell_h_nroHab = array('posx' => '99.1608062499875', 'posy' => '119.84804791665155', 'data' => $this->h_nrohab[$this->nm_grid_colunas], 'width'      => '0', 'align'      => 'L', 'font_type'  => $this->default_font, 'font_size'  => '12', 'color_r'    => '0', 'color_g'    => '0', 'color_b'    => '0', 'font_style' => $this->default_style);
            $cell_t_nombreTipoHab = array('posx' => '112.1253895833192', 'posy' => '119.26517083331831', 'data' => $this->t_nombretipohab[$this->nm_grid_colunas], 'width'      => '0', 'align'      => 'L', 'font_type'  => 'Helvetica', 'font_size'  => '11', 'color_r'    => '0', 'color_g'    => '0', 'color_b'    => '0', 'font_style' => $this->default_style);
            $cell_t_precioTipoHab = array('posx' => '166', 'posy' => '119', 'data' => $this->t_preciotipohab[$this->nm_grid_colunas], 'width'      => '0', 'align'      => 'L', 'font_type'  => 'Helvetica', 'font_size'  => '11', 'color_r'    => '0', 'color_g'    => '0', 'color_b'    => '0', 'font_style' => $this->default_style);
            $cell_r_fechaRes = array('posx' => '32.7464208333292', 'posy' => '149.84518333331445', 'data' => $this->r_fechares[$this->nm_grid_colunas], 'width'      => '0', 'align'      => 'L', 'font_type'  => 'Helvetica', 'font_size'  => '11', 'color_r'    => '255', 'color_g'    => '255', 'color_b'    => '255', 'font_style' => B);
            $cell_d_precioReservacion = array('posx' => '182', 'posy' => '119.62897291665159', 'data' => $this->t_preciotipohab[$this->nm_grid_colunas], 'width'      => '0', 'align'      => 'L', 'font_type'  => 'Helvetica', 'font_size'  => '11', 'color_r'    => '0', 'color_g'    => '0', 'color_b'    => '0', 'font_style' => $this->default_style);
            $cell_cantidad = array('posx' => '84.93124999998929', 'posy' => '119.59166666665159', 'data' => $this->SC_conv_utf8('1'), 'width'      => '0', 'align'      => 'L', 'font_type'  => 'Helvetica', 'font_size'  => '11', 'color_r'    => '0', 'color_g'    => '0', 'color_b'    => '0', 'font_style' => $this->default_style);
            $cell_subtotal = array('posx' => '182', 'posy' => '253.99999999996797', 'data' => $this->t_preciotipohab[$this->nm_grid_colunas], 'width'      => '0', 'align'      => 'L', 'font_type'  => 'Helvetica', 'font_size'  => '11', 'color_r'    => '0', 'color_g'    => '0', 'color_b'    => '0', 'font_style' => $this->default_style);
            $cell_impuesto = array('posx' => '182', 'posy' => '262.73124999996685', 'data' => $this->SC_conv_utf8('0'), 'width'      => '0', 'align'      => 'L', 'font_type'  => 'Helvetica', 'font_size'  => '11', 'color_r'    => '0', 'color_g'    => '0', 'color_b'    => '0', 'font_style' => $this->default_style);
            $cell_total = array('posx' => '182.0333333333104', 'posy' => '283.66508333329756', 'data' => $this->t_preciotipohab[$this->nm_grid_colunas], 'width'      => '0', 'align'      => 'L', 'font_type'  => 'Helvetica', 'font_size'  => '11', 'color_r'    => '0', 'color_g'    => '0', 'color_b'    => '0', 'font_style' => $this->default_style);


            $this->Pdf->SetFont($cell_d_idDetalle['font_type'], $cell_d_idDetalle['font_style'], $cell_d_idDetalle['font_size']);
            $this->pdf_text_color($cell_d_idDetalle['data'], $cell_d_idDetalle['color_r'], $cell_d_idDetalle['color_g'], $cell_d_idDetalle['color_b']);
            if (!empty($cell_d_idDetalle['posx']) && !empty($cell_d_idDetalle['posy']))
            {
                $this->Pdf->SetXY($cell_d_idDetalle['posx'], $cell_d_idDetalle['posy']);
            }
            elseif (!empty($cell_d_idDetalle['posx']))
            {
                $this->Pdf->SetX($cell_d_idDetalle['posx']);
            }
            elseif (!empty($cell_d_idDetalle['posy']))
            {
                $this->Pdf->SetY($cell_d_idDetalle['posy']);
            }
            $this->Pdf->Cell($cell_d_idDetalle['width'], 0, $cell_d_idDetalle['data'], 0, 0, $cell_d_idDetalle['align']);

            $this->Pdf->SetFont($cell_c_idCliente['font_type'], $cell_c_idCliente['font_style'], $cell_c_idCliente['font_size']);
            $this->pdf_text_color($cell_c_idCliente['data'], $cell_c_idCliente['color_r'], $cell_c_idCliente['color_g'], $cell_c_idCliente['color_b']);
            if (!empty($cell_c_idCliente['posx']) && !empty($cell_c_idCliente['posy']))
            {
                $this->Pdf->SetXY($cell_c_idCliente['posx'], $cell_c_idCliente['posy']);
            }
            elseif (!empty($cell_c_idCliente['posx']))
            {
                $this->Pdf->SetX($cell_c_idCliente['posx']);
            }
            elseif (!empty($cell_c_idCliente['posy']))
            {
                $this->Pdf->SetY($cell_c_idCliente['posy']);
            }
            $this->Pdf->Cell($cell_c_idCliente['width'], 0, $cell_c_idCliente['data'], 0, 0, $cell_c_idCliente['align']);

            $this->Pdf->SetFont($cell_c_nombreCli['font_type'], $cell_c_nombreCli['font_style'], $cell_c_nombreCli['font_size']);
            $this->pdf_text_color($cell_c_nombreCli['data'], $cell_c_nombreCli['color_r'], $cell_c_nombreCli['color_g'], $cell_c_nombreCli['color_b']);
            if (!empty($cell_c_nombreCli['posx']) && !empty($cell_c_nombreCli['posy']))
            {
                $this->Pdf->SetXY($cell_c_nombreCli['posx'], $cell_c_nombreCli['posy']);
            }
            elseif (!empty($cell_c_nombreCli['posx']))
            {
                $this->Pdf->SetX($cell_c_nombreCli['posx']);
            }
            elseif (!empty($cell_c_nombreCli['posy']))
            {
                $this->Pdf->SetY($cell_c_nombreCli['posy']);
            }
            $this->Pdf->Cell($cell_c_nombreCli['width'], 0, $cell_c_nombreCli['data'], 0, 0, $cell_c_nombreCli['align']);

            $this->Pdf->SetFont($cell_c_apellidoCli['font_type'], $cell_c_apellidoCli['font_style'], $cell_c_apellidoCli['font_size']);
            $this->pdf_text_color($cell_c_apellidoCli['data'], $cell_c_apellidoCli['color_r'], $cell_c_apellidoCli['color_g'], $cell_c_apellidoCli['color_b']);
            if (!empty($cell_c_apellidoCli['posx']) && !empty($cell_c_apellidoCli['posy']))
            {
                $this->Pdf->SetXY($cell_c_apellidoCli['posx'], $cell_c_apellidoCli['posy']);
            }
            elseif (!empty($cell_c_apellidoCli['posx']))
            {
                $this->Pdf->SetX($cell_c_apellidoCli['posx']);
            }
            elseif (!empty($cell_c_apellidoCli['posy']))
            {
                $this->Pdf->SetY($cell_c_apellidoCli['posy']);
            }
            $this->Pdf->Cell($cell_c_apellidoCli['width'], 0, $cell_c_apellidoCli['data'], 0, 0, $cell_c_apellidoCli['align']);

            $this->Pdf->SetFont($cell_h_nroHab['font_type'], $cell_h_nroHab['font_style'], $cell_h_nroHab['font_size']);
            $this->pdf_text_color($cell_h_nroHab['data'], $cell_h_nroHab['color_r'], $cell_h_nroHab['color_g'], $cell_h_nroHab['color_b']);
            if (!empty($cell_h_nroHab['posx']) && !empty($cell_h_nroHab['posy']))
            {
                $this->Pdf->SetXY($cell_h_nroHab['posx'], $cell_h_nroHab['posy']);
            }
            elseif (!empty($cell_h_nroHab['posx']))
            {
                $this->Pdf->SetX($cell_h_nroHab['posx']);
            }
            elseif (!empty($cell_h_nroHab['posy']))
            {
                $this->Pdf->SetY($cell_h_nroHab['posy']);
            }
            $this->Pdf->Cell($cell_h_nroHab['width'], 0, $cell_h_nroHab['data'], 0, 0, $cell_h_nroHab['align']);

            $this->Pdf->SetFont($cell_t_nombreTipoHab['font_type'], $cell_t_nombreTipoHab['font_style'], $cell_t_nombreTipoHab['font_size']);
            $this->pdf_text_color($cell_t_nombreTipoHab['data'], $cell_t_nombreTipoHab['color_r'], $cell_t_nombreTipoHab['color_g'], $cell_t_nombreTipoHab['color_b']);
            if (!empty($cell_t_nombreTipoHab['posx']) && !empty($cell_t_nombreTipoHab['posy']))
            {
                $this->Pdf->SetXY($cell_t_nombreTipoHab['posx'], $cell_t_nombreTipoHab['posy']);
            }
            elseif (!empty($cell_t_nombreTipoHab['posx']))
            {
                $this->Pdf->SetX($cell_t_nombreTipoHab['posx']);
            }
            elseif (!empty($cell_t_nombreTipoHab['posy']))
            {
                $this->Pdf->SetY($cell_t_nombreTipoHab['posy']);
            }
            $this->Pdf->Cell($cell_t_nombreTipoHab['width'], 0, $cell_t_nombreTipoHab['data'], 0, 0, $cell_t_nombreTipoHab['align']);

            $this->Pdf->SetFont($cell_t_precioTipoHab['font_type'], $cell_t_precioTipoHab['font_style'], $cell_t_precioTipoHab['font_size']);
            $this->pdf_text_color($cell_t_precioTipoHab['data'], $cell_t_precioTipoHab['color_r'], $cell_t_precioTipoHab['color_g'], $cell_t_precioTipoHab['color_b']);
            if (!empty($cell_t_precioTipoHab['posx']) && !empty($cell_t_precioTipoHab['posy']))
            {
                $this->Pdf->SetXY($cell_t_precioTipoHab['posx'], $cell_t_precioTipoHab['posy']);
            }
            elseif (!empty($cell_t_precioTipoHab['posx']))
            {
                $this->Pdf->SetX($cell_t_precioTipoHab['posx']);
            }
            elseif (!empty($cell_t_precioTipoHab['posy']))
            {
                $this->Pdf->SetY($cell_t_precioTipoHab['posy']);
            }
            $this->Pdf->Cell($cell_t_precioTipoHab['width'], 0, $cell_t_precioTipoHab['data'], 0, 0, $cell_t_precioTipoHab['align']);

            $this->Pdf->SetFont($cell_r_fechaRes['font_type'], $cell_r_fechaRes['font_style'], $cell_r_fechaRes['font_size']);
            $this->pdf_text_color($cell_r_fechaRes['data'], $cell_r_fechaRes['color_r'], $cell_r_fechaRes['color_g'], $cell_r_fechaRes['color_b']);
            if (!empty($cell_r_fechaRes['posx']) && !empty($cell_r_fechaRes['posy']))
            {
                $this->Pdf->SetXY($cell_r_fechaRes['posx'], $cell_r_fechaRes['posy']);
            }
            elseif (!empty($cell_r_fechaRes['posx']))
            {
                $this->Pdf->SetX($cell_r_fechaRes['posx']);
            }
            elseif (!empty($cell_r_fechaRes['posy']))
            {
                $this->Pdf->SetY($cell_r_fechaRes['posy']);
            }
            $this->Pdf->Cell($cell_r_fechaRes['width'], 0, $cell_r_fechaRes['data'], 0, 0, $cell_r_fechaRes['align']);

            $this->Pdf->SetFont($cell_d_precioReservacion['font_type'], $cell_d_precioReservacion['font_style'], $cell_d_precioReservacion['font_size']);
            $this->pdf_text_color($cell_d_precioReservacion['data'], $cell_d_precioReservacion['color_r'], $cell_d_precioReservacion['color_g'], $cell_d_precioReservacion['color_b']);
            if (!empty($cell_d_precioReservacion['posx']) && !empty($cell_d_precioReservacion['posy']))
            {
                $this->Pdf->SetXY($cell_d_precioReservacion['posx'], $cell_d_precioReservacion['posy']);
            }
            elseif (!empty($cell_d_precioReservacion['posx']))
            {
                $this->Pdf->SetX($cell_d_precioReservacion['posx']);
            }
            elseif (!empty($cell_d_precioReservacion['posy']))
            {
                $this->Pdf->SetY($cell_d_precioReservacion['posy']);
            }
            $this->Pdf->Cell($cell_d_precioReservacion['width'], 0, $cell_d_precioReservacion['data'], 0, 0, $cell_d_precioReservacion['align']);

            $this->Pdf->SetFont($cell_cantidad['font_type'], $cell_cantidad['font_style'], $cell_cantidad['font_size']);
            $this->pdf_text_color($cell_cantidad['data'], $cell_cantidad['color_r'], $cell_cantidad['color_g'], $cell_cantidad['color_b']);
            if (!empty($cell_cantidad['posx']) && !empty($cell_cantidad['posy']))
            {
                $this->Pdf->SetXY($cell_cantidad['posx'], $cell_cantidad['posy']);
            }
            elseif (!empty($cell_cantidad['posx']))
            {
                $this->Pdf->SetX($cell_cantidad['posx']);
            }
            elseif (!empty($cell_cantidad['posy']))
            {
                $this->Pdf->SetY($cell_cantidad['posy']);
            }
            $this->Pdf->Cell($cell_cantidad['width'], 0, $cell_cantidad['data'], 0, 0, $cell_cantidad['align']);

            $this->Pdf->SetFont($cell_subtotal['font_type'], $cell_subtotal['font_style'], $cell_subtotal['font_size']);
            $this->pdf_text_color($cell_subtotal['data'], $cell_subtotal['color_r'], $cell_subtotal['color_g'], $cell_subtotal['color_b']);
            if (!empty($cell_subtotal['posx']) && !empty($cell_subtotal['posy']))
            {
                $this->Pdf->SetXY($cell_subtotal['posx'], $cell_subtotal['posy']);
            }
            elseif (!empty($cell_subtotal['posx']))
            {
                $this->Pdf->SetX($cell_subtotal['posx']);
            }
            elseif (!empty($cell_subtotal['posy']))
            {
                $this->Pdf->SetY($cell_subtotal['posy']);
            }
            $this->Pdf->Cell($cell_subtotal['width'], 0, $cell_subtotal['data'], 0, 0, $cell_subtotal['align']);

            $this->Pdf->SetFont($cell_impuesto['font_type'], $cell_impuesto['font_style'], $cell_impuesto['font_size']);
            $this->pdf_text_color($cell_impuesto['data'], $cell_impuesto['color_r'], $cell_impuesto['color_g'], $cell_impuesto['color_b']);
            if (!empty($cell_impuesto['posx']) && !empty($cell_impuesto['posy']))
            {
                $this->Pdf->SetXY($cell_impuesto['posx'], $cell_impuesto['posy']);
            }
            elseif (!empty($cell_impuesto['posx']))
            {
                $this->Pdf->SetX($cell_impuesto['posx']);
            }
            elseif (!empty($cell_impuesto['posy']))
            {
                $this->Pdf->SetY($cell_impuesto['posy']);
            }
            $this->Pdf->Cell($cell_impuesto['width'], 0, $cell_impuesto['data'], 0, 0, $cell_impuesto['align']);

            $this->Pdf->SetFont($cell_total['font_type'], $cell_total['font_style'], $cell_total['font_size']);
            $this->pdf_text_color($cell_total['data'], $cell_total['color_r'], $cell_total['color_g'], $cell_total['color_b']);
            if (!empty($cell_total['posx']) && !empty($cell_total['posy']))
            {
                $this->Pdf->SetXY($cell_total['posx'], $cell_total['posy']);
            }
            elseif (!empty($cell_total['posx']))
            {
                $this->Pdf->SetX($cell_total['posx']);
            }
            elseif (!empty($cell_total['posy']))
            {
                $this->Pdf->SetY($cell_total['posy']);
            }
            $this->Pdf->Cell($cell_total['width'], 0, $cell_total['data'], 0, 0, $cell_total['align']);

          $max_Y = 0;
          $this->rs_grid->MoveNext();
          $this->sc_proc_grid = false;
          $nm_quant_linhas++ ;
      }  
   }  
   $this->rs_grid->Close();
   $this->Pdf->Output($this->Ini->root . $this->Ini->nm_path_pdf, 'F');
 }
 function pdf_text_color(&$val, $r, $g, $b)
 {
     $pos = strpos($val, "@SCNEG#");
     if ($pos !== false)
     {
         $cor = trim(substr($val, $pos + 7));
         $val = substr($val, 0, $pos);
         $cor = (substr($cor, 0, 1) == "#") ? substr($cor, 1) : $cor;
         if (strlen($cor) == 6)
         {
             $r = hexdec(substr($cor, 0, 2));
             $g = hexdec(substr($cor, 2, 2));
             $b = hexdec(substr($cor, 4, 2));
         }
     }
     $this->Pdf->SetTextColor($r, $g, $b);
 }
 function SC_conv_utf8($input)
 {
     if ($_SESSION['scriptcase']['charset'] != "UTF-8" && !NM_is_utf8($input))
     {
         $input = sc_convert_encoding($input, "UTF-8", $_SESSION['scriptcase']['charset']);
     }
     return $input;
 }
   function nm_conv_data_db($dt_in, $form_in, $form_out)
   {
       $dt_out = $dt_in;
       if (strtoupper($form_in) == "DB_FORMAT") {
           if ($dt_out == "null" || $dt_out == "")
           {
               $dt_out = "";
               return $dt_out;
           }
           $form_in = "AAAA-MM-DD";
       }
       if (strtoupper($form_out) == "DB_FORMAT") {
           if (empty($dt_out))
           {
               $dt_out = "null";
               return $dt_out;
           }
           $form_out = "AAAA-MM-DD";
       }
       if (strtoupper($form_out) == "SC_FORMAT_REGION") {
           $this->nm_data->SetaData($dt_in, strtoupper($form_in));
           $prep_out  = (strpos(strtolower($form_in), "dd") !== false) ? "dd" : "";
           $prep_out .= (strpos(strtolower($form_in), "mm") !== false) ? "mm" : "";
           $prep_out .= (strpos(strtolower($form_in), "aa") !== false) ? "aaaa" : "";
           $prep_out .= (strpos(strtolower($form_in), "yy") !== false) ? "aaaa" : "";
           return $this->nm_data->FormataSaida($this->nm_data->FormatRegion("DT", $prep_out));
       }
       else {
           nm_conv_form_data($dt_out, $form_in, $form_out);
           return $dt_out;
       }
   }
   function nm_gera_mask(&$nm_campo, $nm_mask)
   { 
      $trab_campo = $nm_campo;
      $trab_mask  = $nm_mask;
      $tam_campo  = strlen($nm_campo);
      $trab_saida = "";
      $str_highlight_ini = "";
      $str_highlight_fim = "";
      if(substr($nm_campo, 0, 23) == '<div class="highlight">' && substr($nm_campo, -6) == '</div>')
      {
           $str_highlight_ini = substr($nm_campo, 0, 23);
           $str_highlight_fim = substr($nm_campo, -6);

           $trab_campo = substr($nm_campo, 23, -6);
           $tam_campo  = strlen($trab_campo);
      }      $mask_num = false;
      for ($x=0; $x < strlen($trab_mask); $x++)
      {
          if (substr($trab_mask, $x, 1) == "#")
          {
              $mask_num = true;
              break;
          }
      }
      if ($mask_num )
      {
          $ver_duas = explode(";", $trab_mask);
          if (isset($ver_duas[1]) && !empty($ver_duas[1]))
          {
              $cont1 = count(explode("#", $ver_duas[0])) - 1;
              $cont2 = count(explode("#", $ver_duas[1])) - 1;
              if ($tam_campo >= $cont2)
              {
                  $trab_mask = $ver_duas[1];
              }
              else
              {
                  $trab_mask = $ver_duas[0];
              }
          }
          $tam_mask = strlen($trab_mask);
          $xdados = 0;
          for ($x=0; $x < $tam_mask; $x++)
          {
              if (substr($trab_mask, $x, 1) == "#" && $xdados < $tam_campo)
              {
                  $trab_saida .= substr($trab_campo, $xdados, 1);
                  $xdados++;
              }
              elseif ($xdados < $tam_campo)
              {
                  $trab_saida .= substr($trab_mask, $x, 1);
              }
          }
          if ($xdados < $tam_campo)
          {
              $trab_saida .= substr($trab_campo, $xdados);
          }
          $nm_campo = $str_highlight_ini . $trab_saida . $str_highlight_ini;
          return;
      }
      for ($ix = strlen($trab_mask); $ix > 0; $ix--)
      {
           $char_mask = substr($trab_mask, $ix - 1, 1);
           if ($char_mask != "x" && $char_mask != "z")
           {
               $trab_saida = $char_mask . $trab_saida;
           }
           else
           {
               if ($tam_campo != 0)
               {
                   $trab_saida = substr($trab_campo, $tam_campo - 1, 1) . $trab_saida;
                   $tam_campo--;
               }
               else
               {
                   $trab_saida = "0" . $trab_saida;
               }
           }
      }
      if ($tam_campo != 0)
      {
          $trab_saida = substr($trab_campo, 0, $tam_campo) . $trab_saida;
          $trab_mask  = str_repeat("z", $tam_campo) . $trab_mask;
      }
   
      $iz = 0; 
      for ($ix = 0; $ix < strlen($trab_mask); $ix++)
      {
           $char_mask = substr($trab_mask, $ix, 1);
           if ($char_mask != "x" && $char_mask != "z")
           {
               if ($char_mask == "." || $char_mask == ",")
               {
                   $trab_saida = substr($trab_saida, 0, $iz) . substr($trab_saida, $iz + 1);
               }
               else
               {
                   $iz++;
               }
           }
           elseif ($char_mask == "x" || substr($trab_saida, $iz, 1) != "0")
           {
               $ix = strlen($trab_mask) + 1;
           }
           else
           {
               $trab_saida = substr($trab_saida, 0, $iz) . substr($trab_saida, $iz + 1);
           }
      }
      $nm_campo = $str_highlight_ini . $trab_saida . $str_highlight_ini;
   } 
}
?>
