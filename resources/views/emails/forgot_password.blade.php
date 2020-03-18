@extends('emails.template')

@section('emails.main')
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border:none;border-collapse:collapse;border-spacing:0;max-width:700px;width:100%">
        <tbody><tr>
          <td style="background-color:#ffffff" align="center"> 
			<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border:none;border-collapse:collapse;border-spacing:0;margin:auto;max-width:700px;width:100%" class="m_8700322356242046330tron">
              <tbody><tr>
                <td align="center"><table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border:none;border-collapse:collapse;border-spacing:0;margin:auto;width:100%" bgcolor="#ffffff" class="m_8700322356242046330basetable">
                    <tbody><tr>
                      <td align="center">
                        
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="m_8700322356242046330basetable" style="border:none;border-collapse:collapse;border-spacing:0;width:100%">
                          <tbody><tr>
                            <td align="center" style="background-color:#ffffff"> 
                              
                              
                              <table border="0" cellpadding="0" cellspacing="0" width="100%" class="m_8700322356242046330basetable" style="border:none;border-collapse:collapse;border-spacing:0;width:100%">
                                <tbody><tr>
                                  <td>
                                    
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="m_8700322356242046330basetable" style="border:none;border-collapse:collapse;border-spacing:0;width:100%">
                                      <tbody><tr>
                                        <td> 
                                           
                                          
                                          
                                          
                                          <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border:none;border-collapse:collapse;border-spacing:0;width:100%">
                                            <tbody><tr>
                                              <td class="m_8700322356242046330outsidegutter" align="left" style="padding:0 14px 0 14px"><table border="0" cellpadding="0" cellspacing="0" style="border:none;border-collapse:collapse;border-spacing:0;width:100%">
                                                  <tbody><tr>
                                                    <td>
                                                      
                                                      <table border="0" cellpadding="0" cellspacing="0" class="m_8700322356242046330t1of12 m_8700322356242046330layout" align="left" style="border:none;border-collapse:collapse;border-spacing:0;max-width:56px;width:100%">
                                                        <tbody><tr>
                                                          <td style="font-size:12px;line-height:1px;padding-left:0px;padding-right:0px"><table border="0" cellpadding="0" cellspacing="0" class="m_8700322356242046330basetable" width="100%" align="left" style="border:none;border-collapse:collapse;border-spacing:0;table-layout:fixed;width:100%">
                                                              <tbody><tr>
                                                                <td>&nbsp;</td>
                                                              </tr>
                                                            </tbody></table></td>
                                                        </tr>
                                                      </tbody></table>
                                                      
                                                       
                                                       
                                                      
                                                      
                                                      <table border="0" cellpadding="0" cellspacing="0" class="m_8700322356242046330t10of12 m_8700322356242046330basetable" align="left" style="border:none;border-collapse:collapse;border-spacing:0;max-width:560px;width:100%">
                                                        <tbody><tr>
                                                          <td style="padding-left:12px;padding-right:12px">
                                                            
                                                            <table border="0" cellpadding="0" cellspacing="0" style="border:none;border-collapse:collapse;border-spacing:0;width:100%" width="100%">
                                                              <tbody><tr>
                                                                <td class="m_8700322356242046330lhreset" style="font-size:0px;line-height:0px;padding-bottom:20px">&nbsp;</td>
                                                              </tr>
                                                            </tbody></table>
                                                            
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                          <td style="padding-left:12px;padding-right:12px"><table border="0" cellpadding="0" cellspacing="0" class="m_8700322356242046330basetable" width="100%" align="left" style="border:none;border-collapse:collapse;border-spacing:0;table-layout:fixed;width:100%">
                                                              <tbody><tr>
                                                                <td class="m_8700322356242046330p1" style="color:#717172;font-family:'ClanPro-Book','HelveticaNeue-Light','Helvetica Neue Light',Helvetica,Arial,sans-serif;font-size:16px;line-height:28px">Hello {{ $content['first_name'] }},<br><br> 
                                                                  
                                                                  We received a request to reset your {{$site_name}} password. Click the link below to choose a new one:<br><br> 

                                                                  <a style="text-decoration:none;font-family:'ClanPro-Book','HelveticaNeue-Light','Helvetica Neue Light',Helvetica,Arial,sans-serif;font-size:16px;color:#12939a" href="{{ $content['url'].('reset_password?secret='.$content['token']) }}" target="_blank" data-saferedirecturl="{{ $content['url'].('reset_password?secret='.$content['token']) }}"> Reset Your Password</a>

                                                                  </td>
                                                              </tr>
                                                              <tr>
                                                                <td>
                                                                  
                                                                  <table border="0" cellpadding="0" cellspacing="0" style="border:none;border-collapse:collapse;border-spacing:0;width:100%" width="100%">
                                                                    <tbody><tr>
                                                                      <td class="m_8700322356242046330p1-noLH m_8700322356242046330lhreset" style="font-size:0px;line-height:0px;padding-bottom:28px">&nbsp;</td>
                                                                    </tr>
                                                                  </tbody></table>
                                                                  
                                                                  </td>
                                                              </tr>
                                                              <tr>
                                                                <td>
                                                                  
                                                                  <table border="0" cellpadding="0" cellspacing="0" style="border:none;border-collapse:collapse;border-spacing:0;width:100%" width="100%">
                                                                    <tbody><tr>
                                                                      <td class="m_8700322356242046330p1-noLH m_8700322356242046330lhreset" style="font-size:0px;line-height:0px;padding-bottom:54px">&nbsp;</td>
                                                                    </tr>
                                                                  </tbody></table>
                                                                  
                                                                  </td>
                                                              </tr>
                                                            </tbody></table></td>
                                                        </tr>
                                                      </tbody></table>
                                                      
                                                       
                                                       
                                                      
                                                      
                                                      <table border="0" cellpadding="0" cellspacing="0" class="m_8700322356242046330t1of12 m_8700322356242046330layout" align="left" style="border:none;border-collapse:collapse;border-spacing:0;max-width:56px;width:100%">
                                                        <tbody><tr>
                                                          <td style="font-size:12px;line-height:1px;padding-left:0px;padding-right:0px"><table border="0" cellpadding="0" cellspacing="0" class="m_8700322356242046330basetable" width="100%" align="left" style="border:none;border-collapse:collapse;border-spacing:0;table-layout:fixed;width:100%">
                                                              <tbody><tr>
                                                                <td>&nbsp;</td>
                                                              </tr>
                                                            </tbody></table></td>
                                                        </tr>
                                                      </tbody></table>
                                                      
                                                      </td>
                                                  </tr>
                                                </tbody></table></td>
                                            </tr>
                                          </tbody></table>
                                          
                                           
                                           
                                          </td>
                                      </tr>
                                    </tbody></table>
                                    
                                    </td>
                                </tr>
                              </tbody></table>
                              
                              </td>
                          </tr>
                        </tbody></table>
                        
                        </td>
                    </tr>
                  </tbody></table></td>
              </tr>
            </tbody></table>
            </td>
        </tr>
      </tbody></table>
@stop