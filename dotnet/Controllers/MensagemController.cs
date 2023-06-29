using Microsoft.AspNetCore.Mvc;
using Microsoft.Extensions.Configuration;
using MySql.Data.MySqlClient;
using RabbitMQ.Client;
using RabbitMQ.Client.Events;
using System;
using System.Text;

namespace dotnet.Controllers
{
  [ApiController]
  [Route("[controller]")]
  public class MensagemController : ControllerBase
  {
    private readonly IConfiguration _configuration;

    public MensagemController(IConfiguration configuration)
    {
      _configuration = configuration;
    }

    [HttpPost]
    public IActionResult Post(string mensagem)
    {
      // Gravar mensagem no banco de dados
      string connectionString = _configuration.GetConnectionString("DefaultConnection");
      using MySqlConnection connection = new MySqlConnection(connectionString);
      connection.Open();

      using MySqlCommand command = new MySqlCommand("INSERT INTO Mensagens (Texto) VALUES (@texto)", connection);
      command.Parameters.AddWithValue("@texto", mensagem);
      command.ExecuteNonQuery();

      return Ok();
    }

    [HttpGet]
    public IActionResult Get()
    {
      // Consumir mensagem do RabbitMQ
      string connectionUrl = _configuration.GetValue<string>("RabbitMQ:ConnectionUrl");
      string queueName = _configuration.GetValue<string>("RabbitMQ:QueueName");

      var factory = new ConnectionFactory { Uri = new Uri(connectionUrl) };
      using var connection = factory.CreateConnection();
      using var channel = connection.CreateModel();

      channel.QueueDeclare(queue: queueName, durable: false, exclusive: false, autoDelete: false, arguments: null);

      var consumer = new EventingBasicConsumer(channel);
      string mensagem = null;
      consumer.Received += (model, ea) =>
      {
        var body = ea.Body.ToArray();
        var mensagem = Encoding.UTF8.GetString(body);
        Post(mensagem);
      };

      channel.BasicConsume(queue: queueName, autoAck: true, consumer: consumer);

      return Ok(mensagem);

    }
  }
}
