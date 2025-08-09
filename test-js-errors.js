const puppeteer = require('puppeteer');

(async () => {
  try {
    const browser = await puppeteer.launch({
      headless: true,
      args: ['--no-sandbox', '--disable-setuid-sandbox']
    });
    
    const page = await browser.newPage();
    
    // Capturar erros JavaScript
    page.on('console', msg => {
      if (msg.type() === 'error') {
        console.log('❌ ERRO JS:', msg.text());
      } else if (msg.type() === 'warning') {
        console.log('⚠️  WARNING JS:', msg.text());
      }
    });
    
    page.on('pageerror', error => {
      console.log('❌ ERRO DE PÁGINA:', error.message);
    });
    
    page.on('requestfailed', request => {
      console.log('❌ FALHA DE REQUEST:', request.url(), request.failure().errorText);
    });
    
    console.log('🔍 Carregando página de registro...');
    await page.goto('http://127.0.0.1:8000/register', { 
      waitUntil: 'networkidle2',
      timeout: 30000 
    });
    
    console.log('✅ Página carregada com sucesso!');
    
    // Testar campos específicos
    const dateField = await page.$('#ready_at_om_date_display');
    const hiddenDateField = await page.$('#ready_at_om_date');
    
    if (dateField && hiddenDateField) {
      console.log('✅ Campos de data encontrados');
      
      // Testar clique no campo de data
      await page.click('#ready_at_om_date_display');
      console.log('✅ Clique no campo de data executado');
      
    } else {
      console.log('❌ Campos de data não encontrados');
    }
    
    // Verificar se funções JavaScript existem
    const functions = await page.evaluate(() => {
      return {
        openDatePicker: typeof openDatePicker !== 'undefined',
        updateDisplayDate: typeof updateDisplayDate !== 'undefined',
        toggleSectionField: typeof toggleSectionField !== 'undefined'
      };
    });
    
    console.log('🔍 Funções JavaScript:', functions);
    
    await browser.close();
    console.log('✅ Teste concluído sem erros críticos!');
    
  } catch (error) {
    console.log('❌ ERRO CRÍTICO:', error.message);
  }
})();
