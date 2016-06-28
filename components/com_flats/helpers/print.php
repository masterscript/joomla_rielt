<?php
defined( '_JEXEC' ) or die;
class BlogIcons{
    
    /**
     * Метод для добавления кнопки печати
     * @param $url
     * @param array $attribs
     * @return mixed
     */
    static function print_popup( $url, $attribs = array() )
    {
        //добавляем ссылку которая сформирует окно печати
        $url .= '?tmpl=component&print=1&layout=default';
        //параметры окна печати
        $status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';
        //Иконка печати
        $text = JHtml::_( 'image', 'system/printButton.png', JText::_( 'JGLOBAL_PRINT' ), NULL, true );
        //парамтры кнопки отправки
        $attribs['title'] = JText::_( 'JGLOBAL_PRINT' );
        $attribs['onclick'] = "window.open(this.href,'win2','" . $status . "'); return false;";
        $attribs['rel'] = 'nofollow';
        //возвращаем сформированную кнопку печати
        return JHtml::_( 'link', JRoute::_( $url ), $text, $attribs );
    }
}